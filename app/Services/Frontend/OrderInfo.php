<?php

namespace App\Services\Frontend;

use App\Models\Order as OrderModel;
use App\Validators\Order as OrderValidator;

class OrderInfo extends Service
{

    public function getOrderInfo()
    {
        $sn = $this->request->getQuery('sn');

        $validator = new OrderValidator();

        $order = $validator->checkOrderBySn($sn);

        return $this->handleOrderInfo($order);
    }

    /**
     * @param OrderModel $order
     * @return array
     */
    protected function handleOrderInfo($order)
    {
        $order->item_info = $this->handleItemInfo($order);

        $result = [
            'sn' => $order->id,
            'subject' => $order->subject,
            'amount' => $order->amount,
            'status' => $order->status,
            'user_id' => $order->user_id,
            'item_id' => $order->item_id,
            'item_type' => $order->item_type,
            'item_info' => $order->item_info,
            'created_at' => $order->created_at,
        ];

        return $result;
    }

    /**
     * @param OrderModel $order
     * @return array
     */
    protected function handleItemInfo($order)
    {
        /**
         * @var array $itemInfo
         */
        $itemInfo = $order->item_info;

        if ($order->item_type == OrderModel::ITEM_COURSE) {

            return $this->handleCourseInfo($itemInfo);

        } elseif ($order->item_type == OrderModel::ITEM_PACKAGE) {

            return $this->handlePackageInfo($itemInfo);

        } elseif ($order->item_type == OrderModel::ITEM_VIP) {

            return $this->handleVipInfo($itemInfo);
        }

        return $itemInfo;
    }

    /**
     * @param array $itemInfo
     * @return array
     */
    protected function handleCourseInfo($itemInfo)
    {
        $itemInfo['course']['cover'] = kg_img_url($itemInfo['course']['cover']);

        return $itemInfo;
    }

    /**
     * @param array $itemInfo
     * @return array
     */
    protected function handlePackageInfo($itemInfo)
    {
        $imgBaseUrl = kg_img_base_url();

        foreach ($itemInfo['package']['courses'] as &$course) {
            $course['cover'] = $imgBaseUrl . $course['cover'];
        }

        return $itemInfo;
    }

    /**
     * @param array $itemInfo
     * @return array
     */
    protected function handleVipInfo($itemInfo)
    {
        return $itemInfo;
    }

}
