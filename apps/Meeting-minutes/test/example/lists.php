<?php
/*
 * @Description: 会议记录列表展示页面
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 19:20:08
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

require_once '../../../../common/common.php';

use Hs\Fmproject\Minutes;

$minuteIndex = Minutes\Index::getInstance();

// 分页参数处理
$paged = !empty($_GET['paged']) && is_numeric($_GET['paged']) && $_GET['paged'] > 0 ? intval($_GET['paged']) : 1; // 当前页码
$per_page = !empty($_GET['per_page']) && is_numeric($_GET['per_page']) ? intval($_GET['per_page']) : 10; // 每页显示的记录数

$params = [
  'paged' => $paged,
  'per_page' => $per_page,
];
$minutes = $minuteIndex->getItems($params);

// 调试输出API返回内容
// var_dump($minutes);

// 计算总页数 - 确保从API中正确获取total字段
$total = isset($minutes['total']) ? intval($minutes['total']) : (isset($minutes['items']) ? count($minutes['items']) : 0);
$total_pages = $minutes['max_pages'];

$detailApi = 'http://innovaoaloc.cn/apps/meeting-minutes/test/example/detailapi.php';
$minuteApi = 'http://innovaoaloc.cn/apps/meeting-minutes/test/example/minuteapi.php';
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>会议记录管理系统</title>
  <!-- CSS & JS -->
  <link href="https://2innova.hardsun.cn/tools/oa-feedback/static/css/bootstrap.min.css" rel="stylesheet" type="text/css">

  <link href="https://hardsun.cn/assets/common/vendors/general/toastr/build/toastr.css" rel="stylesheet" type="text/css">
  <!--end:: COMMON STYLES -->


  <link href="https://hardsun.cn/assets/common/css/main.min.css" rel="stylesheet" type="text/css">


  <!-- oa feedback form css -->
  <link href="https://2innova.hardsun.cn/tools/oa-feedback/static/css/main.min.css" rel="stylesheet" type="text/css" />



  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <!-- Font Awesome 图标库 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .hs-meeting-row {
      cursor: pointer;
    }

    .hs-meeting-detail {
      display: none;
      background-color: #f8f9fa;
    }

    .hs-detail-container {
      padding: 15px;
      border-top: 1px solid #dee2e6;
    }

    .hs-expand-icon {
      transition: transform 0.3s;
    }

    .hs-rotate-icon {
      transform: rotate(180deg);
    }

    .hs-detail-row {
      margin-bottom: 8px;
      border-left: 3px solid #6c757d;
      padding-left: 15px;
    }

    .hs-empty-details {
      padding: 20px;
      text-align: center;
      color: #6c757d;
    }

    .hs-meeting-date {
      width: 120px;
    }

    .hs-meeting-title {
      font-weight: 500;
    }

    .hs-actions-column {
      width: 100px;
    }

    /* .hs-badge {
      font-size: 85%;
    } */

    /* 分页样式增强 */
    .hs-pagination {
      margin-bottom: 0;
      padding-left: 0;
    }

    .hs-pagination>li {
      display: inline-block;
    }

    .hs-pagination>li>a,
    .hs-pagination>li>span {
      padding: 6px 12px;
      border: 1px solid #ddd;
      margin-left: -1px;
      float: left;
      text-decoration: none;
      background-color: #fff;
      color: #337ab7;
    }

    .hs-pagination>.active>a {
      background-color: #337ab7;
      color: white;
      border-color: #337ab7;
    }

    .hs-pagination>.disabled>a,
    .hs-pagination>.disabled>span {
      color: #777;
      cursor: not-allowed;
    }

    .hs-page-info {
      color: #6c757d;
    }

    .hs-pagination-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    /* Bootstrap 3.4 兼容样式 */
    .hs-btn-sm {
      padding: 5px 10px;
      font-size: 12px;
    }

    .hs-margin-right-5 {
      margin-right: 5px;
    }

    .hs-margin-left-5 {
      margin-left: 5px;
    }

    .hs-margin-bottom-15 {
      margin-bottom: 15px;
    }

    .hs-padding-0 {
      padding: 0;
    }

    .flex-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    /* 替代 d-flex */
    .hs-flex {
      display: flex;
    }

    .hs-align-items-center {
      display: flex;
      align-items: center;
    }

    .hs-text-danger {
      color: #d9534f;
    }

    .hs-text-muted {
      color: #777;
    }

    /* Toast 通知样式 */
    .hs-toast-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 9999;
    }

    .hs-small {
      font-size: 85%;
    }

    @media (max-width: 768px) {
      .hs-pagination-container {
        flex-direction: column;
        gap: 10px;
      }
    }

    .hs-header-row {
      display: flex;
      align-items: flex-end;
      /* 垂直底部对齐 */
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .hs-header-title {
      flex: 1;
    }

    .hs-header-actions {
      text-align: right;
      align-self: flex-end;
      /* 确保在底部 */
    }

    /* 添加基本网格系统 */
    .hs-row {
      display: flex;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
      width: 100%;
    }

    .hs-col-xs-12,
    .hs-col-sm-4,
    .hs-col-md-6 {
      position: relative;
      min-height: 1px;
      padding-right: 15px;
      padding-left: 15px;
    }

    /* 移动设备优先，小屏幕占满宽度 */
    .hs-col-xs-6 {
      width: 50%;
      float: left;
    }

    .hs-col-xs-12 {
      width: 100%;
      float: left;
    }

    /* 中等屏幕尺寸 */
    @media (min-width: 768px) {
      .hs-col-sm-4 {
        width: 33.33333333%;
        float: left;
      }
    }

    /* 文字对齐 */
    .hs-text-center {
      text-align: center;
    }

    .hs-text-right {
      text-align: right;
    }

    /* 模态框表单样式修复 */
    .hs-form-horizontal .hs-form-group {
      display: flex;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
      margin-bottom: 15px;
    }

    .hs-form-horizontal .hs-control-label {
      padding-top: 7px;
      margin-bottom: 0;
      text-align: right;
    }

    .hs-modal-dialog {
      width: 90%;
      max-width: 900px; /* 根据需要调整 */
      margin: 30px auto;
    }

    .hs-modal-lg {
      width: 90%;
      max-width: 900px;
    }

    .hs-modal-content {
      padding: 20px;
    }

    .hs-modal-header {
      padding: 15px;
      border-bottom: 1px solid #e5e5e5;
      position: relative;
      min-height: 16.42px;  /* 确保有足够高度容纳关闭按钮 */
    }

    .hs-modal-header .hs-close {
      position: absolute;
      right: 15px;
      top: 15px;
      margin-top: 0;
    }

    .hs-modal-title {
      margin: 0;
      line-height: 1.42857143;
    }

    /* 确保modal-body类与hs-modal-body保持一致 */
    .modal-body {
      position: relative;
      padding: 15px;
    }

    /* 确保modal-footer类与hs-modal-footer保持一致 */
    .modal-footer {
      padding: 15px;
      text-align: right;
      border-top: 1px solid #e5e5e5;
    }
    /* 在CSS中添加或修改以下样式 */
    .hs-modal-header {
      padding: 15px;
      border-bottom: 1px solid #e5e5e5;
      position: relative;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* .hs-close {
      float: right;
      font-size: 21px;
      font-weight: bold;
      line-height: 1;
      color: #000;
      text-shadow: 0 1px 0 #fff;
      opacity: .2;
      background: none;
      border: none;
      padding: 0;
      cursor: pointer;
    }

    .hs-close:hover {
      opacity: .5;
    } */

    .hs-margin-right-10 {
      margin-right: 10px; /* 增加边距 */
    }

    /* 优化箭头图标显示 */
    .hs-expand-icon {
      font-size: 14px;    /* 适当调整图标大小 */
      width: 16px;        /* 固定宽度 */
      text-align: center; /* 居中对齐 */
      color: #337ab7;     /* 使用主色调 */
    }

    /* 改进会议记录主题和标题样式 */
    .hs-meeting-title {
      font-weight: 500;
      margin-left: 2px;   /* 微调左边距 */
    }
  </style>
</head>

<body>
  <div class="hs-container-fluid" style="margin-top: 20px; margin-bottom: 30px;">
    <div class="hs-header-row">
      <div class="hs-header-title">
        <h2>会议记录管理</h2>
      </div>
      <div class="hs-header-actions">
        <a href="single.php" class="hs-btn hs-btn-primary">
          <i class="fa fa-plus margin-right-5"></i> 新建会议
        </a>
      </div>
    </div>

    <div class="hs-panel hs-panel-default">
      <div class="hs-panel-body">
        <div class="hs-table-responsive">
          <table class="hs-table hs-table-hover">
            <thead>
              <tr>
                <th scope="col" width="50">#</th>
                <th scope="col">会议记录</th>
                <th scope="col" width="120">日期</th>
                <th scope="col" width="100">主持人</th>
                <th scope="col" width="100">操作</th>
                <th scope="col" width="60">明细</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($minutes['items'])): ?>
                <?php foreach ($minutes['items'] as $index => $meeting): ?>
                  <!-- 会议记录行 -->
                  <tr class="hs-meeting-row" data-meeting-id="<?php echo $meeting['id']; ?>">
                    <td><?php echo $index+1; ?></td>
                    <td>
                      <div class="hs-align-items-center">
                        <i class="fa fa-chevron-down hs-expand-icon hs-margin-right-10"></i>
                        <div>
                          <span class="hs-meeting-title"><?php echo htmlspecialchars($meeting['theme']); ?></span>
                          <div class="hs-small hs-text-muted"><?php echo htmlspecialchars($meeting['title']); ?></div>
                        </div>
                      </div>
                    </td>
                    <td class="hs-meeting-date"><?php echo date('Y-m-d', strtotime($meeting['date'])); ?></td>
                    <td><?php echo htmlspecialchars($meeting['host']); ?></td>
                    <td>
                      <div class="hs-flex">
                        <a href="single.php?meeting_id=<?php echo $meeting['id']; ?>" class="hs-btn hs-btn-sm hs-btn-primary margin-right-5" title="编辑">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button class="hs-btn hs-btn-sm hs-btn-danger delete-meeting-btn" data-meeting-id="<?php echo $meeting['id']; ?>" title="删除">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                    <td>
                      <span class="hs-label hs-label-primary">
                        <?php echo count($meeting['details']); ?>项
                      </span>
                    </td>
                  </tr>

                  <!-- 会议明细展开区域 -->
                  <tr class="hs-meeting-detail" id="details-<?php echo $meeting['id']; ?>">
                    <td colspan="6" class="hs-padding-0">
                      <div class="hs-detail-container">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                          <div>
                            <h5 style="margin-top: 0; margin-bottom: 0;">会议明细</h5>
                          </div>
                          <div>
                            <button class="hs-btn hs-btn-sm hs-btn-success add-detail-btn"
                              data-meeting-id="<?php echo $meeting['id']; ?>">
                              <i class="fas fa-plus"></i> 添加明细
                            </button>
                          </div>
                        </div>

                        <!-- 会议摘要信息 -->
                        <div class="hs-panel hs-panel-default hs-margin-bottom-15">
                          <div class="hs-panel-heading">
                            会议信息
                          </div>
                          <div class="hs-panel-body">
                            <table style="width:100%">
                              <tr>
                                <td style="width:50%; vertical-align:top; padding-right:15px;">
                                  <p><strong>记录人：</strong> <?php echo htmlspecialchars($meeting['recorder']); ?></p>
                                  <p><strong>参会人员：</strong> <?php echo !empty($meeting['participants']) ? htmlspecialchars($meeting['participants']) : '无记录'; ?></p>
                                  <p><strong>缺席人员：</strong> <?php echo !empty($meeting['absentees']) ? htmlspecialchars($meeting['absentees']) : '无'; ?></p>
                                </td>
                                <td style="width:50%; vertical-align:top;">
                                  <p><strong>会议总结：</strong></p>
                                  <div class="hs-well" style="max-height:150px; overflow-y:auto">
                                    <?php echo nl2br(htmlspecialchars($meeting['summary'])); ?>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </div>
                        </div>

                        <!-- 明细列表 -->
                        <?php if (!empty($meeting['details'])): ?>
                          <div class="hs-table-responsive">
                            <table class="hs-table">
                              <thead>
                                <tr>
                                  <th>责任人</th>
                                  <th>会议内容</th>
                                  <th>解决方案</th>
                                  <th class="hs-actions-column">操作</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($meeting['details'] as $detail): ?>
                                  <tr class="hs-detail-item" data-detail-id="<?php echo $detail['id']; ?>">
                                    <td>
                                      <strong><?php echo htmlspecialchars($detail['responsible_person']); ?></strong>
                                      <?php if (!empty($detail['department'])): ?>
                                        <div class="hs-small hs-text-muted"><?php echo htmlspecialchars($detail['department']); ?></div>
                                      <?php endif; ?>
                                    </td>
                                    <td>
                                      <div style="white-space:pre-line">
                                        <?php echo htmlspecialchars($detail['meeting_content']); ?>
                                      </div>
                                    </td>
                                    <td>
                                      <div style="white-space:pre-line">
                                        <?php echo htmlspecialchars($detail['solution']); ?>
                                      </div>
                                      <?php if (!empty($detail['planned_completion_time'])): ?>
                                        <div class="hs-text-muted hs-small hs-margin-top-5">
                                          <i class="fas fa-calendar"></i> 计划完成:
                                          <?php echo htmlspecialchars($detail['planned_completion_time']); ?>
                                        </div>
                                      <?php endif; ?>
                                    </td>
                                    <td>
                                      <div class="hs-flex">
                                        <button class="hs-btn hs-btn-sm hs-btn-primary hs-margin-right-5 edit-detail-btn"
                                          data-detail-id="<?php echo $detail['id']; ?>"
                                          data-meeting-id="<?php echo $meeting['id']; ?>">
                                          <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="hs-btn hs-btn-sm hs-btn-danger delete-detail-btn"
                                          data-detail-id="<?php echo $detail['id']; ?>">
                                          <i class="fas fa-trash"></i>
                                        </button>
                                      </div>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        <?php else: ?>
                          <div class="hs-empty-details">
                            <p><i class="fas fa-info-circle me-2"></i>暂无明细记录</p>
                          </div>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="hs-text-center" style="padding: 20px;">暂无会议记录</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <!-- 替换现有的分页控件为表格布局 -->
        <div class="hs-pagination-container" style="margin-top: 20px;">
          <table style="width: 100%;" class="hs-pagination-table">
            <tr>
              <td style="width: 33%; text-align: left; vertical-align: middle;">
                <div class="hs-page-info">
                  <?php if ($total > 0): ?>
                    显示 <?php echo ($paged - 1) * $per_page + 1; ?> - <?php echo min($paged * $per_page, $total); ?> 条，共 <?php echo $total; ?> 条记录
                  <?php else: ?>
                    暂无记录
                  <?php endif; ?>
                </div>
              </td>
              
              <td style="width: 34%; text-align: center; vertical-align: middle;">
                <div class="hs-text-center">
                  <ul class="hs-pagination">
                    <!-- 上一页 -->
                    <li class="<?php echo $paged <= 1 ? 'disabled' : ''; ?>">
                      <a href="<?php echo $paged <= 1 ? 'javascript:void(0)' : '?paged='.($paged - 1).'&per_page='.$per_page; ?>">
                        <span>&laquo;</span>
                      </a>
                    </li>
                    
                    <?php
                    // 计算显示的页码范围
                    $start_page = max(1, $paged - 2);
                    $end_page = min($total_pages, $paged + 2);

                    // 如果当前页接近开始，则显示更多后面的页码
                    if ($start_page <= 3) {
                      $end_page = min($total_pages, 5);
                    }

                    // 如果当前页接近结束，则显示更多前面的页码
                    if ($end_page >= $total_pages - 2) {
                      $start_page = max(1, $total_pages - 4);
                    }

                    // 第一页
                    if ($start_page > 1) {
                      echo '<li><a href="?paged=1&per_page=' . $per_page . '">1</a></li>';
                      if ($start_page > 2) {
                        echo '<li class="disabled"><span>...</span></li>';
                      }
                    }

                    // 页码
                    for ($i = $start_page; $i <= $end_page; $i++) {
                      $active = $i == $paged ? 'active' : '';
                      echo '<li class="' . $active . '"><a href="?paged=' . $i . '&per_page=' . $per_page . '">' . $i . '</a></li>';
                    }

                    // 最后一页
                    if ($end_page < $total_pages) {
                      if ($end_page < $total_pages - 1) {
                        echo '<li class="disabled"><span>...</span></li>';
                      }
                      echo '<li><a href="?paged=' . $total_pages . '&per_page=' . $per_page . '">' . $total_pages . '</a></li>';
                    }
                    ?>
                    
                    <!-- 下一页 -->
                    <li class="<?php echo $paged >= $total_pages ? 'disabled' : ''; ?>">
                      <a href="<?php echo $paged >= $total_pages ? 'javascript:void(0)' : '?paged='.($paged + 1).'&per_page='.$per_page; ?>">
                        <span>&raquo;</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
              
              <td style="width: 33%; text-align: right; vertical-align: middle;">
                <div style="display: inline-block;">
                  <label class="hs-control-label hs-margin-right-5">每页显示:</label>
                  <select class="hs-form-control hs-input-sm" id="perPageSelect" style="width: auto; display: inline-block;">
                    <option value="5" <?php echo $per_page == 5 ? 'selected' : ''; ?>>5条</option>
                    <option value="10" <?php echo $per_page == 10 ? 'selected' : ''; ?>>10条</option>
                    <option value="20" <?php echo $per_page == 20 ? 'selected' : ''; ?>>20条</option>
                    <option value="50" <?php echo $per_page == 50 ? 'selected' : ''; ?>>50条</option>
                  </select>
                </div>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- 明细编辑模态框 -->
  <div class="hs-modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="hs-modal-dialog hs-modal-lg">
      <div class="hs-modal-content">
        <div class="hs-modal-header">
          <h4 class="hs-modal-title" id="detailModalLabel">会议明细</h4>
          <button type="button" class="hs-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="hs-modal-body">
          <form id="detailForm" class="hs-form-horizontal">
            <input type="hidden" id="detailId" name="id">
            <input type="hidden" id="minutesId" name="minutes_id">
            <input type="hidden" id="actionType" name="action" value="create">

            <div class="hs-row">
              <div class="hs-col-md-6">
                <div class="hs-form-group">
                  <label for="department" class="hs-col-sm-4 hs-control-label">责任部门</label>
                  <div class="hs-col-sm-8">  <!-- 这里从col-sm-8修改为hs-col-sm-8 -->
                    <input type="text" class="hs-form-control" id="department" name="department">
                  </div>
                </div>
              </div>
              <div class="hs-col-md-6">
                <div class="hs-form-group">
                  <label for="responsiblePerson" class="hs-col-sm-4 hs-control-label">责任人 <span class="hs-text-danger">*</span></label>
                  <div class="hs-col-sm-8">
                    <input type="text" class="hs-form-control" id="responsiblePerson" name="responsible_person" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="hs-form-group">
              <label for="meetingContent" class="hs-col-sm-2 hs-control-label">会议内容 <span class="hs-text-danger">*</span></label>
              <div class="hs-col-sm-10">  <!-- 这里修改 -->
                <textarea class="hs-form-control" id="meetingContent" name="meeting_content" rows="4" required></textarea>
              </div>
            </div>

            <div class="hs-form-group">
              <label for="solution" class="hs-col-sm-2 hs-control-label">解决方案 <span class="hs-text-danger">*</span></label>
              <div class="hs-col-sm-10">  <!-- 这里从col-sm-10修改为hs-col-sm-10 -->
                <textarea class="hs-form-control" id="solution" name="solution" rows="4" required></textarea>
              </div>
            </div>

            <div class="hs-row">
              <div class="hs-col-md-6">
                <div class="hs-form-group">
                  <label for="plannedCompletionTime" class="hs-col-sm-4 hs-control-label">计划完成时间</label>
                  <div class="hs-col-sm-8">
                    <input type="text" class="hs-form-control" id="plannedCompletionTime" name="planned_completion_time">
                  </div>
                </div>
              </div>
              <div class="hs-col-md-6">
                <div class="hs-form-group">
                  <label for="cooperatingDepartment" class="hs-col-sm-4 hs-control-label">配合部门</label>
                  <div class="hs-col-sm-8">
                    <input type="text" class="hs-form-control" id="cooperatingDepartment" name="cooperating_department">
                  </div>
                </div>
              </div>
            </div>

            <div class="hs-form-group">
              <label for="others" class="hs-col-sm-2 hs-control-label">其他</label>
              <div class="hs-col-sm-10">
                <textarea class="hs-form-control" id="others" name="others" rows="2"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="hs-btn hs-btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="hs-btn hs-btn-primary" id="saveDetailBtn">保存</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 删除明细确认模态框 -->
  <div class="hs-modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="hs-modal-dialog">
      <div class="hs-modal-content">
        <div class="hs-modal-header">
          <button type="button" class="hs-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="hs-modal-title">删除确认</h4>
        </div>
        <div class="hs-modal-body">
          <p>确定要删除这条会议明细吗？此操作不可撤销。</p>
        </div>
        <div class="hs-modal-footer">
          <button type="button" class="hs-btn hs-btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="hs-btn hs-btn-danger" id="confirmDeleteBtn">确认删除</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 删除会议确认模态框 -->
  <div class="hs-modal fade" id="deleteMeetingModal" tabindex="-1" role="dialog">
    <div class="hs-modal-dialog">
      <div class="hs-modal-content">
        <div class="hs-modal-header">
          <button type="button" class="hs-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="hs-modal-title">删除会议确认</h4>
        </div>
        <div class="hs-modal-body">
          <p class="hs-text-danger"><i class="fa fa-exclamation-triangle margin-right-5"></i>警告：删除会议记录将同时删除所有相关明细！</p>
          <p>确定要删除这条会议记录吗？此操作不可撤销。</p>
        </div>
        <div class="hs-modal-footer">
          <button type="button" class="hs-btn hs-btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="hs-btn hs-btn-danger" id="confirmDeleteMeetingBtn">确认删除</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap & jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
  
  <script src="https://hardsun.cn/assets/common/vendors/general/block-ui/jquery.blockUI.js" type="text/javascript"></script>

  <script src="https://hardsun.cn/assets/common/vendors/general/toastr/build/toastr.min.js" type="text/javascript"></script>

  <script src="https://hardsun.cn/assets/common/vendors/general/tinymce/tinymce.5.6.1.min.js" type="text/javascript"></script>


  <script src="https://hardsun.cn/assets/common/vendors/general/jquery-validation/dist/jquery.validate.js" type="text/javascript"></script>
  <script src="https://hardsun.cn/assets/common/vendors/general/jquery-validation/dist/additional-methods.js" type="text/javascript"></script>
  <script src="https://hardsun.cn/assets/common/vendors/custom/js/vendors/jquery-validation.init.js" type="text/javascript"></script>


  <script type='text/javascript' src='https://hardsun.cn/assets/common/js/common.min.js' defer onload=''></script>
  <script>
    $(document).ready(function() {
      const detailApiUrl = '<?php echo $detailApi; ?>';
      const minuteApiUrl = '<?php echo $minuteApi; ?>';
      let currentDetailId = null;
      let currentMeetingId = null;

      
      // // 初始化模态框
      // const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
      // const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
      // const deleteMeetingModal = new bootstrap.Modal(document.getElementById('deleteMeetingModal'));
      // const toast = new bootstrap.Toast(document.getElementById('toastNotification'));

      // 每页显示数量变化时
      $('#perPageSelect').on('change', function() {
        const perPage = $(this).val();
        window.location.href = `?paged=1&per_page=${perPage}`;
      });

      // 展开/收起会议明细 - 修复点击事件
      $('.hs-meeting-row').on('click', function(e) {
        // 只有在非按钮区域点击时展开
        if ($(e.target).closest('.hs-btn, button, a, .delete-meeting-btn, .edit-detail-btn, .delete-detail-btn').length > 0) {
          return;
        }

        const meetingId = $(this).data('meeting-id');
        const detailRow = $(`#details-${meetingId}`);
        const expandIcon = $(this).find('.hs-expand-icon');

        // 切换展开状态
        detailRow.toggle();
        expandIcon.toggleClass('hs-rotate-icon');

        // 关闭其他已展开的明细
        if (detailRow.is(':visible')) {
          $('.hs-meeting-detail').not(detailRow).hide();
          $('.hs-meeting-row').not(this).find('.hs-expand-icon').removeClass('hs-rotate-icon');
        }
      });

      // 删除会议按钮点击事件
      $(document).on('click', '.delete-meeting-btn', function(e) {
        e.stopPropagation(); // 阻止事件冒泡，避免展开明细
        currentMeetingId = $(this).data('meeting-id');
        $('#deleteMeetingModal').modal('show');
      });

      // 确认删除会议
      $('#confirmDeleteMeetingBtn').on('click', function() {
        if (!currentMeetingId) return;

        $.ajax({
          url: minuteApiUrl,
          type: 'POST',
          data: {
            action: 'delete',
            id: currentMeetingId
          },
          success: function(response) {
            const result = typeof response === 'object' ? response : JSON.parse(response);

            if (result.code === 0) {
              // 删除成功
              showNotification('会议记录删除成功', 'success');
              // 刷新页面
              setTimeout(() => location.reload(), 1000);
            } else {
              // 删除失败
              showNotification('删除失败: ' + result.message, 'danger');
            }

            $('#deleteMeetingModal').modal('hide');
          },
          error: function(xhr, status, error) {
            console.error("API错误:", status, error, xhr.responseText);
            showNotification('网络错误，请稍后重试', 'danger');
            $('#deleteMeetingModal').modal('hide');
          }
        });
      });

      // 编辑明细按钮点击事件
      $(document).on('click', '.edit-detail-btn', function(e) {
        e.stopPropagation(); // 阻止事件冒泡
        const detailId = $(this).data('detail-id');
        const meetingId = $(this).data('meeting-id');
        currentDetailId = detailId;

        // 查找明细数据 
        const meetingsData = <?php echo json_encode($minutes['items']); ?>;
        let detailData = null;

        // 遍历会议数据找到对应的明细
        for (const meeting of meetingsData) {
          if (meeting.id == meetingId && meeting.details) {
            for (const detail of meeting.details) {
              if (detail.id == detailId) {
                detailData = detail;
                break;
              }
            }
          }
          if (detailData) break;
        }

        if (detailData) {
          // 填充表单
          $('#detailId').val(detailData.id);
          $('#minutesId').val(meetingId);
          $('#department').val(detailData.department);
          $('#responsiblePerson').val(detailData.responsible_person);
          $('#meetingContent').val(detailData.meeting_content);
          $('#solution').val(detailData.solution);
          $('#plannedCompletionTime').val(detailData.planned_completion_time);
          $('#cooperatingDepartment').val(detailData.cooperating_department);
          $('#others').val(detailData.others);
          $('#actionType').val('update');
          $('#detailModalLabel').text('编辑会议明细');

          $('#detailModal').modal('show');
        } else {
          showNotification('未找到明细数据', 'danger');
          console.error('未找到明细数据:', detailId, meetingId);
        }
      });

      // 删除明细按钮点击事件
      $(document).on('click', '.delete-detail-btn', function(e) {
        e.stopPropagation(); // 阻止事件冒泡
        currentDetailId = $(this).data('detail-id');
        $('#deleteModal').modal('show');
      });

      // 确认删除明细
      $('#confirmDeleteBtn').on('click', function() {
        if (!currentDetailId) return;

        $.ajax({
          url: detailApiUrl,
          type: 'POST',
          data: {
            action: 'delete',
            id: currentDetailId
          },
          success: function(response) {
            const result = typeof response === 'object' ? response : JSON.parse(response);

            if (result.code === 0) {
              // 删除成功
              showNotification('明细删除成功', 'success');
              // 刷新页面
              setTimeout(() => location.reload(), 1000);
            } else {
              // 删除失败
              showNotification('删除失败: ' + result.message, 'danger');
            }

            $('#deleteModal').modal('hide');
          },
          error: function(xhr, status, error) {
            console.error("API错误:", status, error, xhr.responseText);
            showNotification('网络错误，请稍后重试', 'danger');
            $('#deleteModal').modal('hide');
          }
        });
      });

      // 保存明细
      $('#saveDetailBtn').on('click', function() {
        const form = document.getElementById('detailForm');

        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          // 高亮所有必填项
          $('input[required], textarea[required]').each(function() {
            if (!$(this).val()) {
              $(this).parent().addClass('has-error');
            } else {
              $(this).parent().removeClass('has-error');
            }
          });
          return;
        }

        // 收集表单数据
        const formData = {};
        $('#detailForm').serializeArray().forEach(item => {
          formData[item.name] = item.value;
        });

        $.ajax({
          url: detailApiUrl,
          type: 'POST',
          data: formData,
          success: function(response) {
            const result = typeof response === 'object' ? response : JSON.parse(response);

            if (result.code === 0) {
              // 操作成功
              const actionType = $('#actionType').val();
              showNotification(actionType === 'create' ? '明细添加成功' : '明细更新成功', 'success');

              // 关闭模态框并刷新页面
              $('#detailModal').modal('hide');
              setTimeout(() => location.reload(), 1000);
            } else {
              // 操作失败
              showNotification('操作失败: ' + result.message, 'danger');
            }
          },
          error: function(xhr, status, error) {
            console.error("API错误:", status, error, xhr.responseText);
            showNotification('网络错误，请稍后重试', 'danger');
          }
        });
      });

      // 添加明细按钮点击事件
      $('.add-detail-btn').on('click', function(e) {
        e.stopPropagation();
        const meetingId = $(this).data('meeting-id');

        // 重置表单
        $('#detailForm')[0].reset();
        $('#detailId').val('');
        $('#minutesId').val(meetingId);
        $('#actionType').val('create');
        $('#detailModalLabel').text('添加会议明细');

        $('#detailModal').modal('show');
      });

      // 显示通知提示 - 使用toastr
      function showNotification(message, type) {
        toastr.options = {
          closeButton: true,
          progressBar: true,
          positionClass: "toast-bottom-right",
          timeOut: 5000
        };

        switch (type) {
          case 'success':
            toastr.success(message);
            break;
          case 'error':
            toastr.error(message);
            break;
          case 'warning':
            toastr.warning(message);
            break;
          default:
            toastr.info(message);
        }
      }
    });
  </script>
</body>

</html>