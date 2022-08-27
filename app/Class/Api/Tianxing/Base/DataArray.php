<?php
namespace App\Class\Api\Tianxing\Base;

class DataArray
{
    /**
     * @var int 索引
     */
    protected int $i = 0;

    /**
     * @var array 数据
     */
    protected array $data;

    public function getNextData()
    {
        if (!isset($this->data[$this->i])) {
            return null;
        }
        return $this->data[$this->i++];
    }

    public function __construct(array $data)
    {
        $this->data=$data;
    }
}
