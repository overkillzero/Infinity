<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\DetectLog;
use App\Models\DetectRule;
use App\Models\DetectBanLog;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class BanController extends AdminController
{
    public function index(ServerRequest $request, Response $response, array $args)
    {
        $table_config['total_column'] = [
            
            'id'     => 'ID',
            'name'   => '名称',
            'text'   => '介绍',
            'regex'  => '正则表达式',
            'type'   => '类型',
            'action' => 'Thao tác',
        ];
        $table_config_ban_record['total_column'] = [
            'id'                => 'ID',
            'user_id'           => '用户ID',
            'detect_number'     => '违规次数',
            'ban_time'          => '封禁时长(分钟)',
            'end_time'          => '封禁开始时间',
            'ban_end_time'      => '封禁结束时间',
            'all_detect_number' => '累计违规次数'
        ];
        $table_config_detect_record['total_column'] = [
            'id'       => 'ID',
            'user_id'  => '用户ID',
            'node_id'  => '节点ID',
            'list_id'  => '规则ID',
            'created_at' => '时间'
        ];
        $table_config_detect_record['ajax_url'] = 'ban/detect/record/ajax';
        $table_config_ban_record['ajax_url']    = 'ban/record/ajax';
        $table_config['ajax_url']               = 'ban/rule/ajax';
        $this->view()
            ->assign('table_config', $table_config)
            ->assign('table_config_detect_record', $table_config_detect_record)
            ->assign('table_config_ban_record', $table_config_ban_record)
            ->display('admin/ban.tpl');
        return $response;
    }

    public function banRuleAjax(ServerRequest $request, Response $response, array $args): Response
    {
        $query = DetectRule::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['action'])) {
                    $order_field = 'id';
                }
            }
        );

        $data = $query['datas']->map(function($rowData) {
            return [
                'id'     => $rowData->id,
                'name'   => $rowData->name,
                'text'   => $rowData->text,
                'regex'  => $rowData->regex,
                'type'   => $rowData->type(),
                'action' => <<<EOT
                                <div class="btn-group dropstart"><a class="btn btn-light-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">Thao tác</a>
                                    <ul    class = "dropdown-menu">
                                    <li><a class = "dropdown-item" onclick = "zeroAdminUpdateBanRule('request', {$rowData->id})">Chỉnh sửa</a></li>
                                    <li><a class = "dropdown-item" type    = "button" onclick = "zeroAdminDelete('ban/rule', {$rowData->id})">Xóa bỏ</a></li>
                                    </ul>
                                </div>
                            EOT,
            ];
        })->toArray();

        return $response->withJson([
            'draw'            => $request->getParsedBodyParam('draw'),
            'recordsTotal'    => DetectRule::count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }

    public function createBanRule(ServerRequest $request, Response $response, array $args): Response
    {
        $postData    = $request->getParsedBody();
        $rule        = new DetectRule();
        $rule->name  = $postData['name'];
        $rule->text  = $postData['text'];
        $rule->regex = $postData['regex'];
        $rule->type  = $postData['type'];

        if (!$rule->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '添加失败'
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功'
        ]);
    }

    public function updateBanRule(ServerRequest $request, Response $response, array $args): Response
    {
        $putData = $request->getParsedBody();
        $id      = $putData['id'];
        $rule    = DetectRule::find($id);

        $rule->name  = $putData['name'];
        $rule->text  = $putData['text'];
        $rule->regex = $putData['regex'];
        $rule->type  = $putData['type'];

        if (!$rule->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '修改失败'
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => 'Sửa đổi thành công'
        ]);
    }

    public function deleteBanRule(ServerRequest $request, Response $response, array $args): Response
    {
        $id   = $request->getParsedBodyParam('id');
        $rule = DetectRule::find($id);
        $rule->delete();
        return $response->withJson([
            'ret' => 1,
            'msg' => 'Xóa thành công'
        ]);
    }

    public function detectRuleRecordAjax(ServerRequest $request, Response $response, array $args): Response
    {
        $query = DetectLog::getTableDataFromAdmin(
            $request,
        );

        $data = $query['datas']->map(function($rowData) {
            return [
                'id'         => $rowData->id,
                'user_id'    => $rowData->user_id,
                'node_id'    => $rowData->node_id,
                'list_id'    => $rowData->list_id,
                'created_at' => date('Y-m-d H:i:s', $rowData->created_at),
            ];
        })->toArray();

        return $response->withJson([
            'draw'            => $request->getParsedBodyParam('draw'),
            'recordsTotal'    => DetectLog::count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }

    public function banRecordAjax(ServerRequest $request, Response $response, array $args): Response
    {
        $query = DetectBanLog::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['ban_end_time'])) {
                    $order_field = 'end_time';
                }
            }
        );

        $data = $query['datas']->map(function($rowData) {
            return [
                'id'                => $rowData->id,
                'user_id'           => $rowData->user_id,
                'detect_number'     => $rowData->detect_number,
                'ban_time'          => $rowData->ban_time,
                'end_time'          => date('Y-m-d H:i:s', $rowData->end_time),
                'ban_end_time'      => date('Y-m-d H:i:s', $rowData->end_time + $rowData->ban_time*60),
                'all_detect_number' => $rowData->all_detect_number,
            ];
        })->toArray();

        return $response->withJson([
            'draw'            => $request->getParsedBodyParam('draw'),
            'recordsTotal'    => DetectBanLog::count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }

    public function requestBanRule(ServerRequest $request, Response $response, array $args): Response
    {
        $id   = $request->getParsedBodyParam('id');
        $rule = DetectRule::find($id);
        return $response->withJson([
            'name'  => $rule->name,
            'id'    => $rule->id,
            'text'  => $rule->text,
            'regex' => $rule->regex,
            'type'  => $rule->type
        ]);
    }
}