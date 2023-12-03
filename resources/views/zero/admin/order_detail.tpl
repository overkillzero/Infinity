<!DOCTYPE html>
<html lang="vi">
    <head>
	<title>{$config["website_name"]} Order Detail</title>
        
        <meta charset="UTF-8" />
        <meta name="renderer" content="webkit" />
        <meta name="description" content="Updates and statistics" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="format-detection" content="telephone=no,email=no" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
        <meta http-equiv="expires" content="0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
        <link href="/theme/zero/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
        <link href="/theme/zero/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
        <link href="/theme/zero/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
        <link href="/favicon.png" rel="shortcut icon">
        <link href="/apple-touch-icon.png" rel="apple-touch-icon">
    </head>
    {include file ='admin/menu.tpl'}
                    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                        <div class="d-flex flex-column flex-column-fluid mt-10">
                            <div id="kt_app_content" class="app-content flex-column-fluid">
                                <div id="kt_app_content_container" class="app-container container-xxl">
                                    
                                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                                        
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Đơn hàng #{$order->order_no}</h2>
                                            </div>
                                        </div>                                                                              
                                        <div class="card-body pt-0">
                                            <div class="table-responsive">
                                                
                                                <table class="table align-middle table-striped table-row-bordered gy-5 gs-7 mb-0">
                                                    
                                                    <thead>
                                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                            <th class="min-w-175px">type</th>
                                                            <th class="min-w-70px text-end">status</th>
                                                            <th class="min-w-70px text-end">payment method</th>
                                                            <th class="min-w-100px text-end">order number</th>
                                                            <th class="min-w-70px text-end">quantity</th>
                                                            <th class="min-w-100px text-end">price</th>
                                                            <th class="min-w-100px text-end">total</th>
                                                        </tr>
                                                    </thead>
                                                    
                                                    
                                                    <tbody class="fw-semibold text-gray-600">
                                                        
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="ms-5">
                                                                        <a class="fw-bold text-gray-600 text-hover-primary">{$order_type[$order->order_type]}</a>
                                                                        <div class="fs-7 text-muted">date: {date('Y-m-d H:i:s', $order->created_at)}</div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </td>
                                                            
                                                            <td class="text-end">
                                                                {if $order->order_status == '1'}
                                                                <span class="badge badge-warning fs-6 fw-bold">pending</span>
                                                                {else if $order->order_status == '2'}
                                                                <span class="badge badge-success fs-6 fw-bold">paid</span>
																{else if $order->order_status == '0'}
																<span class="badge badge-danger fs-6 fw-bold">invalid</span>	
                                                                {/if}   
                                                            </td>
															<td class="text-end">{$order->payment()}</td>
                                                            <td class="text-end">{$order->order_no}</td>                                                           
                                                            <td class="text-end">1</td>
                                                            {if $order->order_type == '2'}
                                                                <td class="text-end">{$order->order_total}</td>                                                           
                                                                <td class="text-end">{$order->order_total}</td>
                                                            {else}
                                                                <td class="text-end">{$order->product_price}</td>                                                           
                                                                <td class="text-end">{$order->product_price}</td>
                                                            {/if}
                                                        </tr>
                                                        {if $order->order_type != '2'}                                                                                                            
                                                        <tr>
                                                            <td colspan="6" class="text-end">余额抵扣</td>
                                                            <td class="text-end">{$order->credit_paid}</td>
                                                        </tr>                                                       
                                                        <tr>
                                                            <td colspan="6" class="text-end">discount</td>
                                                            <td class="text-end">{$order->discount_amount}</td>
                                                        </tr>
                                                        {/if}														                                                        
                                                        <tr>
                                                            <td colspan="6" class="fs-3 text-dark text-end">total</td>
                                                            <td class="text-dark fs-3 fw-bolder text-end">{$order->order_total}</td>
                                                        </tr>
														
														{if $order->order_status == '2'}
														<tr>
                                                            <td colspan="6" class="fs-3 text-dark text-end">paid</td>
                                                            <td class="text-dark fs-3 fw-bolder text-end">{$order->order_total}</td>
                                                        </tr>
														{/if}
                                                        
                                                    </tbody>
                                                    
                                                </table>
                                                
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        {include file='admin/footer.tpl'}
                    </div>
                </div>
            </div>
        </div>
        {include file='admin/script.tpl'}
    </body>
</html>