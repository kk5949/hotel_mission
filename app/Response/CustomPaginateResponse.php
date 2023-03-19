<?php

namespace App\Response;

trait CustomPaginateResponse
{
    // 불필요한 페이지네이션 정보 감춤
    public function customPaginateResponse($data){
        return collect($data)->only(['data', 'current_page', 'last_page', 'per_page', 'total']);
    }
}
