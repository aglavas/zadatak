<?php

namespace Src;

use Objects\PaginateLinksObject;

class Paginator
{

    public static function paginate($offset, $limit, $total_items)
    {
        $final_total = 0;
        $final_current = 0;
        $bool_last = 0;
        $bool_first = 0;

        //total pages
        $total_int = (int) floor($total_items/$limit);
        $total = $total_items/$limit;
        if($total>$total_int)
        {
            $final_total = ++$total_int;
        }else
        {
            $final_total = $total_int;
        }

        $first_page = 1;
        $current_page = $offset+$first_page;

        if($current_page >= $final_total)
        {
            $final_current = $final_total;
            $bool_last = true;
        }
        else
        {
            $final_current = $current_page;
        }

        if($final_current == 1)
        {
            $bool_first = true;
        }

        $obj = new PaginateLinksObject($offset,$limit,$bool_last,$bool_first);

        return $obj;

    }
}