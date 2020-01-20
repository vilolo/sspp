<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/20
 * Time: 14:42
 */

namespace app\controllers;


use PHPExcel_IOFactory;
use yii\web\Controller;

class WeekController extends Controller
{
    public function actionReadExcel(){
        $excel_file = './data/test.xlsx';
        $PHPReader = PHPExcel_IOFactory::load($excel_file);
        foreach ($PHPReader->getWorksheetIterator() as $worksheet) {
            echo '>>>'.$worksheet->getTitle()."<<<\n";
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
                foreach ($cellIterator as $k => $cell) {
                    echo $k . '========' .$cell->getValue();
                    echo '<br>';
                }
            }
        }
    }
}
