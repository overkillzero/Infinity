<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Coupon;
use App\Models\Product;
use App\Utils\Tools;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class CouponController extends AdminController
{
    public function couponIndex(ServerRequest $request, Response $response, array $args): Response
    {
        $table_config['total_column'] = [
            'id'                     => 'ID',
            'code'                   => '优惠码',
            'expire'                 => '过期时间',
            'limited_product'        => '限定商品ID',
            'limited_product_period' => '限定周期',
            'discount'               => '额度',
            'per_use_count'          => '每个用户次数',
            'total_use_count'        => '总使用次数',
            'total_used_count'       => '被使用的次数',
            'action'                 => 'Thao tác',
        ];
        $table_config['ajax_url'] = 'coupon/ajax';
        $products = Product::where('status', 1)->get();
        $this->view()
            ->assign('table_config', $table_config)
            ->assign('products', $products)
            ->display('admin/coupon.tpl');
        return $response;
    }

    public function createCoupon(ServerRequest $request, Response $response, array $args): Response
    {
        $postdata      = $request->getParsedBody();
        $generate_type = $postdata['generate_type'];
        $final_code    = $postdata['code'];
        try {
            if (empty($final_code) && in_array($generate_type, [1, 3])) {
                throw new \Exception('优惠码不能为空');
            }
            if (empty($postdata['discount']) || !is_numeric($postdata['discount'])) {
                throw new \Exception('折扣比例错误');
            }
            if (!empty($postdata['per_use_count']) && $postdata['per_use_count'] <= 0 || !empty($postdata['total_use_count']) && $postdata['total_use_count'] <= 0) {         
                throw new \Exception('次数必须大于0');
            }

            if ($generate_type == 1) {
                if (Coupon::where('code', $final_code)->count() != 0) {
                    throw new \Exception('优惠码已存在');
                }
            } else {
                while (true) {
                    $code_str = Tools::genRandomChar(8);
                    if ($generate_type == 3) {
                        $final_code .= $code_str;
                    } else {
                        $final_code  = $code_str;
                    }

                    if (Coupon::where('code', $final_code)->count() == 0) {
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
        $coupon                         = new Coupon();
        $coupon->per_use_count          = $postdata['per_use_count'] ?: NULL;
        $coupon->total_use_count        = $postdata['total_use_count'] ?: NULL;
        $coupon->code                   = $final_code;
        $coupon->expired_at             = time() + $postdata['expire'] * 3600;
        $coupon->limited_product        = !array_filter($postdata['limited_product']) ? NULL : json_encode($postdata['limited_product']);
        $coupon->limited_product_period = !array_filter($postdata['limited_product_period']) ? NULL : json_encode($postdata['limited_product_period']);
        $coupon->discount               = $postdata['discount'];
        $coupon->created_at             = time();
        $coupon->updated_at             = time();
        $coupon->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '优惠码添加成功'
        ]);
    }

    public function couponAjax(ServerRequest $request, Response $response, array $args): Response
    {
        $query = Coupon::getTableDataFromAdmin(
            $request
        );
        $data = $query['datas']->map(function($rowData) {
            return [
                'id'                     => $rowData->id,
                'code'                   => $rowData->code,
                'expire'                 => date('Y-m-d H:i:s', $rowData->expired_at),
                'limited_product'        => $rowData->limited_product ?? '无限制',
                'limited_product_period' => is_null($rowData->limited_product_period) ? '无限制' : $rowData->getLimitedProductPeriod(),
                'discount'               => $rowData->discount,
                'per_use_count'          => is_null($rowData->per_use_count) ? '无限次使用' : $rowData->per_use_count,
                'total_use_count'        => is_null($rowData->total_use_count) ? '无限次使用' : $rowData->total_use_count,
                'total_used_count'       => $rowData->total_used_count,
                'action'                 => <<<EOT
                                                <div class="btn-group dropstart"><a class="btn btn-light-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">Thao tác</a>
                                                    <ul    class = "dropdown-menu">                                                  
                                                    <li><a class = "dropdown-item" type = "button" onclick = "zeroAdminDelete('coupon', {$rowData->id})">Xóa bỏ</a></li>
                                                    </ul>
                                                </div>
                                            EOT,
            ];
        })->toArray();

        return $response->WithJson([
            'draw'              => $request->getParsedBodyParam('draw'),
            'recordsTotal'      => Coupon::count(),
            'recordsFiltered'   => $query['count'],
            'data'              => $data
        ]);
        
    }

    public function deleteCoupon(ServerRequest $request, Response $response, array $args): Response
    {
        $coupon = Coupon::find($request->getParsedBodyParam('id'));
        $coupon->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => 'Xóa thành công',
        ]);
    }
}