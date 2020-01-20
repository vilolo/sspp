<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/11
 * Time: 17:33
 */

namespace app\controllers;


use PHPExcel_IOFactory;
use Yii;
use yii\web\Controller;

class FinController extends Controller
{
    public function actionInputData(){

        $file_path = 'E:\work_data\finance-data.xls';
        $time = time();
        $dateTime = date('Y-m-d H:i:s',$time);
        $PHPReader = PHPExcel_IOFactory::load($file_path);
        $PHPReader->setActiveSheetIndex(0);
        $sheetData = $PHPReader->getActiveSheet()->toArray(null, true, true, true);

        $stock_data = [];
        foreach ($sheetData as $k => $v){
            if ($k == 1){
                continue;
            }
            $item = [
                'gid' => $v['C'],
                'create_time' => $time,
                'update_time'=> $time,
            ];

            Yii::$app->db->createCommand()->insert('finance_special_unit', $item)->execute();
            $lastId = Yii::$app->db->getLastInsertID();
            $v['A'] = $lastId;
            $v['create_time'] = $dateTime;
            $v['update_time'] = $dateTime;
            //$i = 1;
            foreach ($v as $kk => $vv){
                /**
                 * --[D] => ACGLP-4
                 * --[L] => 產物及意外保險-12
                 * --[AJ] => -36
                 * --[AE] => -31	是否已达约
                 * --[AN] => O-40
                 * --[BM] => 25.00 -65
                 * --[BQ] => -69
                 * --[CA] => -79
                 * --[CB] => -80
                 */
                if (in_array($kk, ['D','L','AJ','AE','AN','BM','BQ','CA','CB'])){
                    unset($v[$kk]);
                    continue;
                }

                if (in_array($kk, ['S','T','U','Y',])){
                    $v[$kk] = str_replace('%', '', $vv);
                }

                if (in_array($kk, ['AX','AY','AZ','CE','CF','CG','CH','CI','CJ','CK'])){
                    $v[$kk] = str_replace(',', '', $vv);
                }
            }
            $stock_data[] = $v;
        }

        Yii::$app->db->createCommand()->batchInsert('finance_stock_extra', $this->sockKey, $stock_data)
        ->execute();
    }

    private $sockKey = [
        "sid",
        "location",
        "income_name",
        "firm_coupon_expire_code",
        "publisher",
        "parent_firm",
        "parent_firm_stock_code",
        "isin",
        "industry",
        "child_industry",
        "currency",
        "next_buy_price",
        "expire_redeem_price",
        "market_buy_back_rate",
        "buy_back_profit_rate",
        "register_state",
        "pre_tax_yield",
        "possible_dividend_tax",
        "after_tax_yield",
        "interest_type",
        //"manual_interest_frequency",
        //"interest_sum",
        //"interest_time",
        "interest_frequency",
        //"interest_json",
        "interest_describe",
        "face_rate",
        "interest_currency",
        "per_share_interest",
        "is_bond",
        "is_accumulate",
        "is_defer",
        "is_stop_interest",
        "has_stop_interest_record",
        "has_guarantee",
        "settlement_sequence",
        "initiator_corporation",
        "underlying_asset",
        "underlying_asset_issuer",
        "credit_standard",
        "credit_moody",
        "credit_rests",
        "issuer_credit_standard",
        "issuer_credit_moody",
        "issuer_credit_rests",
        "issuer_or_firm_credit_standard",
        "issuer_or_firm_credit_moody",
        "issuer_or_firm_credit_rests",
        "outstanding",
        "average_turnover_five",
        "average_turnover_three",
        "limit_five_day",
        "limit_one_month",
        "limit_three_month",
        "limit_one_year",
        "limit_three_year",
        "limit_five_year",
        "limit_ten_year",
        "price_fluctuate",
        "is_buy_back",
        "is_portion_buy_back",
        "buy_back_time",
        "affiche_issue_time",
        "first_buy_back_time",
        "buy_back_affiche_time",
        "practical_buy_back_time",
        "beforehand_day",
        "guess_buy_back_time",
        "last_ex_dividend_time",
        "last_grant_time",
        "next_ex_dividend_time",
        "next_grant_time",
        "expiring_time",
        "is_ahead_buy_back",
        "ahead_buy_back_discounting",
        "compensate_stop_time",
        "is_loss",
        "busi_reta_profits_twelve_month",
        "busi_reta_profits_three_year",
        "busi_flow_twelve_month",
        "busi_flow_three_year",
        "reta_profits_last_year",
        "reta_profits_three_year",
        "common_stock_equity",
        "long_term_debt",
        "special_stock",
        "total_debt",
        "first_cate_capital",
        "bis_ratio",
        "overdue_rate",
        "total_debt_debt",
        "interest_guarantee_multiple",
        "common_stock_limit",
        "optimistic_degree",
        "bourse",
        //"total_profit",
        //"profit_at_maturity",
        "remark",
        "create_time",
        "update_time",];
}
