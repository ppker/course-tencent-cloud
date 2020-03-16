<?php

namespace App\Services\Frontend;

use App\Builders\OrderList as OrderListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\Order as OrderRepo;

class MyOrderList extends Service
{

    use UserTrait;

    public function getUserOrders()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['user_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $orderRepo = new OrderRepo();

        $pager = $orderRepo->paginate($params, $sort, $page, $limit);

        return $this->handleUserOrders($pager);
    }

    public function handleUserOrders($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new OrderListBuilder();

        $orders = $pager->items->toArray();

        $items = [];

        foreach ($orders as $order) {

            $order['item_info'] = $builder->handleItem($order);

            $items[] = [
                'sn' => $order['sn'],
                'subject' => $order['subject'],
                'amount' => $order['amount'],
                'item_id' => $order['item_id'],
                'item_type' => $order['item_type'],
                'item_info' => $order['item_info'],
                'source_type' => $order['source_type'],
                'status' => $order['status'],
                'created_at' => $order['created_at'],
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
