<?php
/**
 * @description web developer helper
 * 
 * @author ZhangHaoLei <debmzhang@163.com>
 * @create 2016-11-26 15:59
 */

namespace debmzhang\toolbox;

class Tool
{
    /**
     * json_encode2
     * 避免中文被转换成unicode
     *
     * @param  mixed $value
     * @return void
     */
    public function json_encode2($value)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            if (is_object($value)) {
                $value = get_object_vars($value);
            }
            $value = $this->_urlencode($value);
            $json = json_encode($value);
            return urldecode($json);
        }
    }

    /**
     * 生成字母+数字
     * 
     * @param int $type 0 纯字母 1 带数字
     */
    public function generateLetters($type = 0, $min = 6, $max = 10)
    {
        $letterStr = $letters = $numbers = '';
        // 字符串长度
        $len = mt_rand($min, $max);
        if (0 == $type) {
            for ($i = 0; $i <= $len; $i++) {
                $letter = mt_rand(97, 122);
                $letterStr .= chr($letter);
            }
        }
        if (1 == $type) {
            // 字母长度
            $letterLen = mt_rand(4, $len);
            // 数字长度
            $numberLen = $len - $letterLen;
            // 字母字符串
            for ($i = 0; $i <= $letterLen; $i++) {
                $letter = mt_rand(97, 122);
                $letters .= chr($letter);
            }
            // 数字字符串
            for ($i = 0; $i <= $numberLen; $i++) {
                $letter = mt_rand(48, 57);
                $numbers .= chr($letter);
            }
            $letterStr = $letters . $numbers;
        }
        return $letterStr;
    }

    /**
     * 验证是否是一个手机号码
     *
     * @param int $phone 手机号码
     */
    public function checkIsPhoneNumber($phone = 0)
    {
        $phone = (int) $phone;
        if (11 != strlen($phone)) {
            return false;
        }
        $pattern = '/^(13|15|17|18)\d{9}$/i';
        if (preg_match($pattern, $phone)) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否是一个正确的身份证号码
     *
     * @param string $id_card 身份证号码
     */
    public function checkIsIdentityCard($id_card = '')
    {
        $idCard = htmlspecialchars($id_card, ENT_QUOTES);
        if (18 != strlen($idCard)) {
            return false;
        }
        // 准备工作
        // 1. 加权因子
        $weightFactor = '7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2';
        $weightFactorArr = explode(',', $weightFactor);
        // 2. 余数 0-10 对应的校验码
        $checkCode = '1,0,X,9,8,7,6,5,4,3,2';
        $checkCodeArr = explode(',', $checkCode);
        // 计算
        // 1. 计算身份证前17位数字各位数字与对应的加权因子的乘积的和
        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += $idCard[$i] * $weightFactorArr[$i];
        }
        // 2. $sum % 11 取余
        $compliment = $sum % 11;
        // 3. 获取余数对应的校验码, 即身份证最后一位数字值
        $lastNum = $checkCodeArr[$compliment];
        // 用户传入的身份证的最后一位值
        $lastFromUser = substr($idCard, -1);
        if ($lastNum == $lastFromUser) {
            return $idCard;
        }
        return false;
    }

    /**
     * _urlencode
     *
     * @param  mixed $value
     * @return void
     */
    protected function _urlencode($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->_urlencode($v);
            }
        } elseif (is_string($value)) {
            $value = urlencode(
                str_replace(
                    array("\r\n", "\r", "\n", "\"", "\/", "\t"),
                    array('\\n', '\\n', '\\n', '\\"', '\\/', '\\t'),
                    $value)
                );
        }
        return $value;
    }

}
