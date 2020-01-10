<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/9
 * Time: 10:33
 */

namespace app\controllers;

use PHPExcel_IOFactory;
use Yii;
use yii\web\Controller;

class ExcelController extends Controller
{
    public function actionIndex(){

        $str = '春节';

        $ss = iconv( "UTF-8", "gb2312" , $str);
        echo $ss;
        echo '<br>';
        echo urlencode($str).PHP_EOL;
        echo urlencode($ss).PHP_EOL;
        echo 'okok';
    }

    public function actionCategory(){
        $file_path = './data/MY-TW category.xlsx';
        $PHPReader = PHPExcel_IOFactory::load($file_path);
        $PHPReader->setActiveSheetIndex(1);
        $sheetData = $PHPReader->getActiveSheet()->toArray(null, true, true, true);

        $res = Yii::$app->db->createCommand()->batchInsert('sp_category',
            ['c_one', 'c_one_zh', 'c_two', 'c_two_zh', 'c_three', 'c_three_zh', 'c_four', 'c_five', 'code', 'platform'],
            $sheetData
        )->execute();

        print_r($res);
        return;
    }

    public function actionGetCategory()
    {
        $all = Yii::$app->db->createCommand('select * from sp_category where platform = "tw"')->queryAll();

//        $data = [];
//        foreach ($one as $k => $v){
//            $data[$v['c_one']] = [];
//            $two = Yii::$app->db
//                ->createCommand("select * from sp_category where c_one = '{$v['c_one']}' and platform = 'tw' group by c_two")
//                ->queryAll();
//            if (!empty($two)){
//                foreach ($two as $k2 => $v2){
//                    $data[$v['c_one']][$v2['c_two']] = [];
//                    $three = Yii::$app->db
//                        ->createCommand("select * from sp_category where c_one = '{$v['c_one']}' and c_two = '{$v['c_two']}' and platform = 'tw' group by c_three")
//                        ->queryAll();
//                    if (!empty($three)){
//                        foreach ($three as $k3 => $v3){
//                            $data[$v['c_one']][$v2['c_two']][$v3['c_three']] = $v3['code'];
//                        }
//                    }
//                }
//            }
//        }

        $data = [];

//        foreach ($all as $k => $v){
//            $one = $v['c_one'].($v['c_one_zh']?"[{$v['c_one_zh']}]":'');
//            $two = $v['c_two'].($v['c_two_zh']?"[{$v['c_two_zh']}]":'');
//            $three = $v['c_three'].($v['c_three_zh']?"[{$v['c_three_zh']}]":'');
//            if (!isset($data[$one])){
//                $data[$one] = [];
//            }
//
//            if (!isset($data[$one][$two])){
//                $data[$one][$two] = [];
//            }
//
//            if (!isset($data[$one][$two][$three])){
//                if ($v['c_four'] == '—'){
//                    $data[$one][$two][$three] = $v['code'];
//                }else{
////                    $data[$v['c_one']][$v['c_two']][$v['c_three']][$v['c_four']] = $v['code'];
//                    $data[$one][$two][$three] = [];
//                }
//            }
//
//            if ($v['c_four'] != '—'){
//                $data[$one][$two][$three][$v['c_four']] = $v['code'];
//            }
//        }

//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";

//        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        $i1 = 1;
        $i2 = 1;
        $i3 = 1;

        foreach ($all as $k => $v){
            $one = $v['c_one'].($v['c_one_zh']?"[{$v['c_one_zh']}]":'');
            $two = $v['c_two'].($v['c_two_zh']?"[{$v['c_two_zh']}]":'');
            $three = $v['c_three'].($v['c_three_zh']?"[{$v['c_three_zh']}]":'');

            $isNewOne = false;
            $isNewTwo = false;
            $isNewThree = false;

            $topKey = 0;

            if (!isset($data[$topKey][$one])){
                $data[$topKey][$one] = $i1;
                $data[$i1] = [];
                $isNewOne = true;
            }

            $oneKey = $data[$topKey][$one];

            if (!isset($data[$oneKey][$two])){
                $data[$oneKey][$two] = $oneKey.'-'.$i2;
                $data[$oneKey.'-'.$i2] = [];
                $isNewTwo = true;
            }

            $twoKey = $data[$oneKey][$two];

            if ($v['c_four'] != '—'){
                if (!isset($data[$twoKey][$three])){
                    $data[$twoKey][$three] = $twoKey.'-'.$i3;
                }
                if (!isset($data[$data[$twoKey][$three]])){
                    $data[$data[$twoKey][$three]] = [];
                }
                $data[$data[$twoKey][$three]][$v['c_four']] = $v['code'];
                $isNewThree = true;
            }else{
                $data[$twoKey][$three] = $v['code'];
            }

            if ($isNewOne) $i1++;
            if ($isNewTwo) $i2++;
            if ($isNewThree) $i3++;
        }

//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        die();
    }
}
