<?php

/*
 * * Zabbix
 * * Copyright (C) 2001-2013 Zabbix SIA
 * *
 * * This program is free software; you can redistribute it and/or modify
 * * it under the terms of the GNU General Public License as published by
 * * the Free Software Foundation; either version 2 of the License, or
 * * (at your option) any later version.
 * *
 * * This program is distributed in the hope that it will be useful,
 * * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * * GNU General Public License for more details.
 * *
 * * You should have received a copy of the GNU General Public License
 * * along with this program; if not, write to the Free Software
 * * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * */

/**
 * @package API
 */
class CItemValue extends CZBXAPI {

    public function GetItemValues($itemInfo) {


        $stime = $itemInfo['stime'];
        $period = $itemInfo['period'];
        $sizeX = $itemInfo['sizeX'];


        $now = time(null);

        if (!isset($stime)) {
            $stime = $now - $period;
        }

        $diffTZ = (date('Z', $stime) - date('Z', $stime + $period));
        $from_time = $stime; // + timeZone offset
        $to_time = $stime + $period; // + timeZone offset

        $p = $to_time - $from_time; // graph size in time
        $z = $p - $from_time % $p; // graphsize - mod(from_time,p) for Oracle...
        $x = $sizeX; // graph size in px
        $config = select_config();
        $real_item = get_item_by_itemid($itemInfo['itemid']);
        $type = $itemInfo['calc_type'];

        $calc_field = 'round(' . $x . '*' . zbx_sql_mod(zbx_dbcast_2bigint('clock') . '+' . $z, $p) . '/(' . $p . '),0)'; // required for 'group by' support of Oracle

        $sql_arr = array();

        // override item history setting with housekeeping settings
        if ($config['hk_history_global']) {
            $real_item['history'] = $config['hk_history'];
        }

        $trendsEnabled = $config['hk_trends_global'] ? ($config['hk_trends'] > 0) : ($real_item['trends'] > 0);



        if (!$trendsEnabled || (($item['history'] * SEC_PER_DAY) > (time() - ($from_time + $period / 2)) && ($period / $sizeX) <= (ZBX_MAX_TREND_DIFF / ZBX_GRAPH_MAX_SKIP_CELL))) { // is reasonable to take data from history?
            //$this->dataFrom = 'history';
            array_push($sql_arr, 'SELECT itemid,' . $calc_field . ' AS i,' .
                    'COUNT(*) AS count,AVG(value) AS avg,MIN(value) as min,' .
                    'MAX(value) AS max,MAX(clock) AS clock' .
                    ' FROM history ' .
                    ' WHERE itemid=' . $itemInfo['itemid'] .
                    ' AND clock>=' . $from_time .
                    ' AND clock<=' . $to_time .
                    ' GROUP BY itemid,' . $calc_field
                    , 'SELECT itemid,' . $calc_field . ' AS i,' .
                    'COUNT(*) AS count,AVG(value) AS avg,MIN(value) AS min,' .
                    'MAX(value) AS max,MAX(clock) AS clock' .
                    ' FROM history_uint ' .
                    ' WHERE itemid=' . $itemInfo['itemid'] .
                    ' AND clock>=' . $from_time .
                    ' AND clock<=' . $to_time .
                    ' GROUP BY itemid,' . $calc_field
            );
        } else {
            //$this->dataFrom = 'trends';
            array_push($sql_arr, 'SELECT itemid,' . $calc_field . ' AS i,' .
                    'SUM(num) AS count,AVG(value_avg) AS avg,MIN(value_min) AS min,' .
                    'MAX(value_max) AS max,MAX(clock) AS clock' .
                    ' FROM trends' .
                    ' WHERE itemid=' . $itemInfo['itemid'] .
                    ' AND clock>=' . $from_time .
                    ' AND clock<=' . $to_time .
                    ' GROUP BY itemid,' . $calc_field
                    , 'SELECT itemid,' . $calc_field . ' AS i,' .
                    'SUM(num) AS count,AVG(value_avg) AS avg,MIN(value_min) AS min,' .
                    'MAX(value_max) AS max,MAX(clock) AS clock' .
                    ' FROM trends_uint ' .
                    ' WHERE itemid=' . $itemInfo['itemid'] .
                    ' AND clock>=' . $from_time .
                    ' AND clock<=' . $to_time .
                    ' GROUP BY itemid,' . $calc_field
            );

            $itemInfo['delay'] = max($itemInfo['delay'], SEC_PER_HOUR);
        }

        $curr_data['count'] = null;
        $curr_data['min'] = null;
        $curr_data['max'] = null;
        $curr_data['avg'] = null;
        $curr_data['clock'] = null;


        foreach ($sql_arr as $sql) {

            $result = DBselect($sql);
            while ($row = DBfetch($result)) {
                $idx = $row['i'] - 1;
                if ($idx < 0) {
                    continue;
                }


                $curr_data['count'][$idx] = $row['count'];
                $curr_data['min'][$idx] = $row['min'];
                $curr_data['max'][$idx] = $row['max'];
                $curr_data['avg'][$idx] = $row['avg'];
                $curr_data['clock'][$idx] = $row['clock'];
                $curr_data['shift_min'][$idx] = 0;
                $curr_data['shift_max'][$idx] = 0;
                $curr_data['shift_avg'][$idx] = 0;
            }
            unset($row);
        }
        $curr_data['avg_orig'] = is_array($curr_data['avg']) ? zbx_avg($curr_data['avg']) : null;
        return $curr_data;
    }

    public function getLastValueBy($data) {
        //$data = &$this->newData[$item['itemid']][$item['calc_type']];
        if (isset($data)) {
            for ($i = 286 - 1; $i >= 0; $i--) {
                if (!empty($data['count'][$i])) {
                    switch ($item['calc_fnc']) {
                        case CALC_FNC_MIN:
                            return $data['min'][$i];
                        case CALC_FNC_MAX:
                            return $data['max'][$i];
                        case CALC_FNC_ALL:
                        case CALC_FNC_AVG:
                        default:
                            return $data['avg'][$i];
                    }
                }
            }
        }
        return 0;
    }

    public function getItemListFormat($list = array()) {
        $itemList = $list['list_item'];
        $parame = $list['parame'];

        $search_key = $parame['item_name_search'];

        $order_result_list = array();

        $search = 0;
        if (empty($search_key)) {
            $search_key_list = array();
        } else {
            $search_key_list = explode(",", $search_key);
        }

        if (count($search_key_list) >= 1) {
            $search = 1;
        }

        foreach ($itemList as $each_item) {
            $each_item = (array) $each_item;

            $item = get_item_by_itemid($each_item['itemid']);

            $newItem = $item;
            // $newItem['name'] = itemName($item);
            $newItem['name'] = $item['name'];

            $newItem['delay'] = getItemDelay($item['delay'], $item['delay_flex']);

            $host = get_host_by_hostid($item['hostid']);

            $newItem['hostname'] = $host['name'];

            if (strpos($item['units'], ',') !== false) {
                list($newItem['units'], $newItem['unitsLong']) = explode(',', $item['units']);
            } else {
                $newItem['unitsLong'] = '';
            }

            if ($search == 0) {
                $newItem['graphid'] = $each_item['graphid'];
                $order_result_list[$newItem['graphid']] = array("graphid" => $newItem['graphid'], "itemname" => $newItem['name'], "lastvalue" => 0, 'min' => 0, 'avg' => 0, 'max' => 0, 'hostname' => $newItem['hostname'], 'chazhi' => 0);
            } else {

                foreach ($search_key_list as $each_search_key) {
                    $each_search_key = trim($each_search_key);
                    //按关键字进行筛选
                    if (strpos(strtolower($newItem['name']), strtolower($each_search_key)) === false) {
                        
                    } else {
                        $newItem['calc_fnc'] = is_null($each_item['calc_fnc']) ? CALC_FNC_AVG : $each_item->calc_fnc;
                        $newItem['calc_type'] = GRAPH_ITEM_SIMPLE;
                        $newItem['graphid'] = $each_item['graphid'];

                        $item_info_new = array_merge($newItem, $parame);

                        $data = self::GetItemValues($item_info_new);

                        if (isset($data) && isset($data['min'])) {

                            $lastvalue = convert_units(self::getLastValueBy($data), $newItem['units'], ITEM_CONVERT_NO_UNITS);
                            $min = convert_units(min($data['min']), $newItem['units'], ITEM_CONVERT_NO_UNITS);
                            $avg = convert_units($data['avg_orig'], $newItem['units'], ITEM_CONVERT_NO_UNITS);
                            $max = convert_units(max($data['max']), $newItem['units'], ITEM_CONVERT_NO_UNITS);
                            //return array("lastvalue"=>$lastvalue,'min'=>$min,'avg'=>$avg,'max'=>$max);
                            $order_result_list[$each_search_key][$newItem['graphid']] = array("graphid" => $newItem['graphid'], "itemname" => $newItem['name'], "lastvalue" => self::getLastValueBy($data), 'min' => min($data['min']), 'avg' => $data['avg_orig'], 'max' => max($data['max']), 'hostname' => $newItem['hostname'], 'chazhi' => max($data['max']) - min($data['min']));
                        } else {
                            $order_result_list[$each_search_key][$newItem['graphid']] = array("graphid" => $newItem['graphid'], "itemname" => $newItem['name'], "lastvalue" => 0, 'min' => 0, 'avg' => 0, 'max' => 0, 'hostname' => $newItem['hostname'], 'chazhi' => 0);
                        }
                        break;
                    }
                }
            }
        }

        return $order_result_list;
    }

}

