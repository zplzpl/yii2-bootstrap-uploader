<?php
/**
 * Created by PhpStorm.
 * User: zhengpenglin
 * Date: 2016/8/1
 * Time: 9:39
 */

$widget = $this->context;

?>

<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadFileModal_<?=$widget->getOption("id");?>"><?=$widget->getOption("text");?></button>

<div id="uploadFileModal_<?=$widget->getOption("id");?>" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?=$widget->getOption("text");?></h4>
            </div>
            <div class="modal-body overlay-wrapper">

                <div id="uploader_<?= $widget->getOption("id"); ?>">
                    <div class="btns">
                        <div id="pick_<?= $widget->getOption("id"); ?>">选择文件</div>
                    </div>
                    <div id="file_list_<?= $widget->getOption("id"); ?>">
                        <table class="table table-striped" id="upload_table_<?= $widget->getOption("id"); ?>">
                            <thead>
                            <tr>
                                <th>文件</th>
                                <th style="width: 200px">进度</th>
                                <th style="width: 40px"></th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="file_upload_empty">
                                <td colspan="4">请选择文件上传...</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


