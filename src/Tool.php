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
     * 生成手机短信校验码
     * 
     * @param int $type 0 纯字母 1 纯数字 2 混合
     * @param int $len 长度 默认6位
     */
    public function generateSmsCode($type = 1, $len = 6)
    {
        $code = '';
        // 纯字母
        if (0 == $type) {
            for ($i = 0; $i < $len; $i++) {
                $ascii = mt_rand(97, 122);
                $code .= chr($ascii);
            }
        }
        // 纯数字
        if (1 == $type) {
            for ($i = 0; $i < $len; $i++) {
                $ascii = mt_rand(48, 57);
                $code .= chr($ascii);
            }
        }
        // 混合
        if (2 == $type) {
            // 去掉易混淆的字母/数字
            $chars = array('2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
            for ($i = 0; $i < $len; $i++) {
                $code .= $chars[mt_rand(0, 31)];
            }
        }
        return $code;
    }

    /**
     * 按年月日时分秒+随机数 生成一个订单
     *
     * @param string $prefix 订单前缀, 默认空
     * @param string $connectors 订单前缀与数字串之间的连接符, 默认空
     * @param int $len 随机数位数, 默认8位
     */
    public function generateOrderNO($prefix = '', $connectors = '', $len = 8)
    {
        $len = (int) $len;
        if (!$len) {
            $len = 8;
        }
        $datetimeStr = date('YmdHis');
        $numberStr = '';
        for ($i = 0; $i < $len; $i++) {
            $ascii = mt_rand(48, 57);
            $numberStr .= chr($ascii);
        }
        return $prefix . $connectors . $datetimeStr . $numberStr;
    }

    /**
     * formatTime
     * 把秒转换成 x天x时x分x秒 的格式
     *
     * @param  mixed $seconds
     */
    public function formatTime($seconds)
    {
        $seconds = intval($seconds);
        $d = floor($seconds / 86400);
        $left = $seconds % 86400;
        $h = floor($left / 3600);
        $left = $left % 3600;
        $m = floor($left / 60);
        $s = $left % 60;
        $map = array(
            '天' => $d,
            '时' => $h,
            '分' => $m,
            '秒' => $s,
        );
        $result = '';
        foreach ($map as $k => $v) {
            if ($v) {
                $result .= $v . $k;
            }
        }
        return $result;
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
            return true;
        }
        return false;
    }

    /**
     * ajaxMessage
     *
     * @param  mixed $code 0:正常 大于0 都是非正常
     * @param  mixed $msg
     * @param  array $result
     * @param  bool $isJsonp 是否使用 jsonp 方式提交数据
     * @param  string $param jsonp 方式提交参数名
     */
    public function ajaxMessage($code = 0, $msg = 'success', $result = array(), $isJsonp = false, $param = 'jsoncallback')
    {
        if ($isJsonp) {
            $jsonData = $this->json_encode2(array(
                'code' => $code,
                'msg' => $msg,
                'result' => $result,
            ));
            echo $param . '(' . $jsonData . ')';
        } else {
            echo $this->json_encode2(array(
                'code' => $code,
                'msg' => $msg,
                'result' => $result,
            ));
        }
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
