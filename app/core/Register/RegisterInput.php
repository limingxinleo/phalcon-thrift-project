<?php
// +----------------------------------------------------------------------
// | Input.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Register;

use App\Core\Register\Exceptions\RegisterException;
use App\Core\Register\Validation\InputValidator;
use App\Utils\Register\Sign;
use JsonSerializable;
use Xin\Thrift\Register\ServiceInfo;

/**
 * Class RegisterInput
 * @package App\Core\Register
 * @property ServiceInfo $input
 */
class RegisterInput implements JsonSerializable
{
    public $input;

    public $inputArray;

    public function __construct(ServiceInfo $input)
    {
        $validator = new InputValidator();
        $inputArray = Sign::serviceInfoToArray($input);
        if ($validator->validate($inputArray)->valid()) {
            throw new RegisterException($validator->getErrorMessage());
        }

        if (!Sign::verify($inputArray, $inputArray['sign'])) {
            throw new RegisterException('The sign is invalid!');
        }

        $this->input = $input;
        $this->inputArray = $inputArray;
    }

    public function toArray()
    {
        return $this->inputArray;
    }

    public function jsonSerialize()
    {
        return json_encode($this->inputArray);
    }


}