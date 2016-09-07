<?php

/**
 * Created by PhpStorm.
 * User: zhengpenglin
 * Date: 2016/7/29
 * Time: 15:05
 */

namespace zplzpl\uploader;

use \yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\Url;

class ImportFileUpload extends Widget
{

    public $name;

    /**
     * 设置参数
     * @var
     * id - 控件ID
     * text - 上传按钮名称
     */
    public $options;

    public function init()
    {
        parent::init();

    }

    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : "";
    }

    public function run()
    {
        $this->options['id'] = isset($this->options['id']) ? $this->options['id'] : sprintf("%05d", rand(0, 100000));
        $this->options['text'] = isset($this->options['text']) ? $this->options['text'] : "上传文件";

        $this->registerClientJs();

        return $this->render("importFileUpload", array());
    }


    private function registerClientJs()
    {
        $baseUrl = ImportFileUploadAsset::register($this->getView())->baseUrl;
        $server = \Yii::$app->getUrlManager()->createAbsoluteUrl("base/uploader/file");
        $id = $this->getOption("id");
        $import = $this->getOption("import");
        $jsCallback = $this->getOption("jsCallback");

        if(empty($jsCallback)){
            $jsCallback = <<<CALLBACK
            {
                    before: function (obj, target) {
                        $(obj).text("导入中...").attr('disabled',true);
                    },
                    success: function (obj, target, data) {
                        $(obj).text("导入成功").removeClass("btn-primary").removeClass("btn-danger").addClass("btn-success");
                    },
                    error:function (obj, target, data) {
                        $(obj).text("再次导入").attr('disabled',false).removeClass("btn-primary").addClass("btn-danger");
                    },
                    exception:function (obj, target) {
                        $(obj).text("再次导入").attr('disabled',false).removeClass("btn-primary").addClass("btn-danger");
                    }
                }
CALLBACK;

        }

        $maxSize = \Yii::$app->params["uploader"]["file"]["maxSize"];
        $ext = \Yii::$app->params["uploader"]["file"]["allowExt"];
        $ext = implode(',', $ext);

        $this->getView()->registerJs(<<<JS

        var uploader_{$id} = null;

        $('#uploadFileModal_{$id}').on('shown.bs.modal', function (e) {
            if(!uploader_{$id} || uploader_{$id}==null || uploader_{$id}==undefined){
                // 初始化上传组件
                uploader_{$id} = WebUploader.create({

                    //上传SWF
                    swf:"{$baseUrl}/Uploader.swf",

                    //请求路由
                    server:"{$server}",

                    //auto {Boolean} [可选] [默认值：false] 设置为 true 后，不需要手动调用上传，有文件选择即开始上传。
                    auto:true,

                    //pick {Selector, Object} [可选] [默认值：undefined] 指定选择文件的按钮容器，不指定则不创建按钮
                    pick:"#pick_{$id}",

                    //fileSingleSizeLimit {int} [可选] [默认值：undefined] 验证单个文件大小是否超出限制, 超出则不允许加入队列。
                    fileSingleSizeLimit:{$maxSize},

                    //accept {Arroy} [可选] [默认值：null] 指定接受哪些类型的文件。 由于目前还有ext转mimeType表，所以这里需要分开指定。
                    accept: {
                        title: 'Files',
                        extensions: '{$ext}'
                    }

                });

                //文件选择错误
                uploader_{$id}.onError = function( code ) {
                    switch(code){
                        case "Q_EXCEED_NUM_LIMIT":
                            alert("文件数量超出限制");
                        break;
                        case "F_EXCEED_SIZE":
                            alert("单个文件大小超出限制：{$maxSize}");
                        break;
                        case "Q_TYPE_DENIED":
                            alert("文件类型只允许上传：{$ext}");
                        break;
                        case "F_DUPLICATE":
                            alert("上传文件重复");
                        default:
                            console.log(code);
                            alert("文件不符合要求");
                        break;
                    }
                };

                // 开始上传前触发
                uploader_{$id}.on( 'uploadStart', function( file ) {
                    console.log("Upload Start");
                    console.log(file);
                    var tbody = $("#upload_table_{$id} tbody");
                    var filename = file.name;
                    var progress = '<div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width: 0%"></div></div>';
                    var progress_label = '<span class="badge bg-green">0%</span>';
                    var action = "<div class='file_action'>上传中...</div>";
                    tbody.append("<tr id='tr_{$id}_"+ file.id +"'><td>"+ filename +"</td><td>"+ progress+"</td><td>"+ progress_label+"</td><td>"+ action+"</td></tr>");
                    console.log(tbody.find(".file_upload_empty").length);
                    if(tbody.find(".file_upload_empty").length>0){
                        tbody.find(".file_upload_empty").remove();
                    }
                });

                // 上传成功
                uploader_{$id}.on( 'uploadSuccess', function( file, data ) {
                    var tr = $("#tr_{$id}_"+file.id);
                    var action = tr.find(".file_action");
                    console.log(file);
                    console.log(data);
                    if(data.code>0){
                        action.html("上传失败");
                        alert(data.msg);
                    }else{
                        var button = '<a href="#" url="{$import}&fileId='+data.result.file.info.id+'" class="btn btn-block btn-xs btn-primary btn-flat import-ajax-{$id}">进行导入</a>';
                        console.log(data.result.file.info);
                        action.html(button);
                    }
                });

                // 上传失败
                uploader_{$id}.on( 'uploadError', function( file, data ) {
                    var tr = $("#tr_{$id}_"+file.id);
                    var action = tr.find(".file_action");
                    action.html("上传失败");
                });

                // 上传完成
                uploader_{$id}.on( 'uploadComplete', function( file ) {
                    var tr = $("#tr_{$id}_"+file.id);
                    var percent = tr.find(".progress");
                    percent.removeClass("active");
                    percent.find(".progress-bar").removeClass("progress-bar-striped");
                });

                // 文件上传过程中创建进度条实时显示。
                uploader_{$id}.on( 'uploadProgress', function( file, percentage ) {
                    console.log("uploadProgress");
                    console.log(file);
                    console.log(percentage);

                    var tr = $("#tr_{$id}_"+file.id);
                    var percent = tr.find(".progress .progress-bar");
                    var label = tr.find(".badge");
                    percent.css( 'width', percentage * 100 + '%' );
                    label.text(percentage * 100 + '%');

                });

                //进入导入按钮事件
                $(".import-ajax-{$id}").ajaxGet({$jsCallback});

            }

        });



JS
        );
    }

}