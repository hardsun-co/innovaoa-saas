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

// 本地路径前缀
$jsPath = '/js/';
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>会议记录管理系统</title>
  <!-- Bootstrap 3.4 CSS -->
  <link href="<?php echo $jsPath; ?>bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $jsPath; ?>toastr.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $jsPath; ?>main.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $jsPath; ?>main2.min.css" rel="stylesheet" type="text/css">

  <!-- Font Awesome 图标库 (本地文件夹中无此文件，保留CDN引用) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/all.min.css">
  <style>
    .meeting-row {
      cursor: pointer;
    }

    .meeting-detail {
      display: none;
      background-color: #f8f9fa;
    }

    .detail-container {
      padding: 15px;
      border-top: 1px solid #dee2e6;
    }

    .expand-icon {
      transition: transform 0.3s;
    }

    .rotate-icon {
      transform: rotate(180deg);
    }

    .detail-row {
      margin-bottom: 8px;
      border-left: 3px solid #6c757d;
      padding-left: 15px;
    }

    .empty-details {
      padding: 20px;
      text-align: center;
      color: #6c757d;
    }

    .meeting-date {
      width: 120px;
    }

    .meeting-title {
      font-weight: 500;
    }

    .actions-column {
      width: 100px;
    }

    /* Bootstrap 3.4 兼容样式 */
    .btn-sm {
      padding: 5px 10px;
      font-size: 12px;
    }

    .margin-right-5 {
      margin-right: 5px;
    }

    .margin-left-5 {
      margin-left: 5px;
    }

    .margin-bottom-15 {
      margin-bottom: 15px;
    }

    .padding-0 {
      padding: 0;
    }

    /* 分页样式增强 */
    .pagination {
      margin-bottom: 0;
    }

    .page-info {
      color: #777;
    }

    .pagination-container {
      margin-top: 20px;
    }

    .flex-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    /* 替代 d-flex */
    .flex {
      display: flex;
    }

    .align-items-center {
      display: flex;
      align-items: center;
    }

    .text-danger {
      color: #d9534f;
    }

    .text-muted {
      color: #777;
    }

    /* Toast 通知样式 */
    .toast-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 9999;
    }

    .small {
      font-size: 85%;
    }

    @media (max-width: 768px) {
      .pagination-container .col-xs-12 {
        margin-bottom: 10px;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid" style="margin-top: 20px; margin-bottom: 30px;">
    <div class="row margin-bottom-15">
      <div class="col-xs-6">
        <h2>会议记录管理</h2>
      </div>
      <div class="col-xs-6 text-right">
        <a href="single.php" class="btn btn-primary">
          <i class="fa fa-plus margin-right-5"></i> 新建会议
        </a>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th width="50">#</th>
                <th>会议记录</th>
                <th width="120">日期</th>
                <th width="100">主持人</th>
                <th width="100">操作</th>
                <th width="60">明细</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($minutes['items'])): ?>
                <?php foreach ($minutes['items'] as $index => $meeting): ?>
                  <!-- 会议记录行 -->
                  <tr class="meeting-row" data-meeting-id="<?php echo $meeting['id']; ?>">
                    <td><?php echo $meeting['id']; ?></td>
                    <td>
                      <div class="align-items-center">
                        <i class="fa fa-chevron-down expand-icon margin-right-5"></i>
                        <div>
                          <span class="meeting-title"><?php echo htmlspecialchars($meeting['theme']); ?></span>
                          <div class="small text-muted"><?php echo htmlspecialchars($meeting['title']); ?></div>
                        </div>
                      </div>
                    </td>
                    <td class="meeting-date"><?php echo date('Y-m-d', strtotime($meeting['date'])); ?></td>
                    <td><?php echo htmlspecialchars($meeting['host']); ?></td>
                    <td>
                      <div class="flex">
                        <a href="single.php?meeting_id=<?php echo $meeting['id']; ?>" class="btn btn-sm btn-primary margin-right-5" title="编辑">
                          <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-meeting-btn" data-meeting-id="<?php echo $meeting['id']; ?>" title="删除">
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </td>
                    <td>
                      <span class="label label-primary">
                        <?php echo count($meeting['details']); ?>项
                      </span>
                    </td>
                  </tr>

                  <!-- 会议明细展开区域 -->
                  <tr class="meeting-detail" id="details-<?php echo $meeting['id']; ?>">
                    <td colspan="6" class="padding-0">
                      <div class="detail-container">
                        <div class="row margin-bottom-15">
                          <div class="col-xs-6">
                            <h5>会议明细</h5>
                          </div>
                          <div class="col-xs-6 text-right">
                            <button class="btn btn-sm btn-success add-detail-btn"
                              data-meeting-id="<?php echo $meeting['id']; ?>">
                              <i class="fa fa-plus"></i> 添加明细
                            </button>
                          </div>
                        </div>

                        <!-- 会议摘要信息 -->
                        <div class="panel panel-default margin-bottom-15">
                          <div class="panel-heading">
                            会议信息
                          </div>
                          <div class="panel-body">
                            <div class="row">
                              <div class="col-md-6">
                                <p><strong>记录人：</strong> <?php echo htmlspecialchars($meeting['recorder']); ?></p>
                                <p><strong>参会人员：</strong> <?php echo !empty($meeting['participants']) ? htmlspecialchars($meeting['participants']) : '无记录'; ?></p>
                                <p><strong>缺席人员：</strong> <?php echo !empty($meeting['absentees']) ? htmlspecialchars($meeting['absentees']) : '无'; ?></p>
                              </div>
                              <div class="col-md-6">
                                <p><strong>会议总结：</strong></p>
                                <div class="well" style="max-height:150px;overflow-y:auto">
                                  <?php echo nl2br(htmlspecialchars($meeting['summary'])); ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- 明细列表 -->
                        <?php if (!empty($meeting['details'])): ?>
                          <div class="table-responsive">
                            <table class="table">
                              <thead>
                                <tr>
                                  <th>责任人</th>
                                  <th>会议内容</th>
                                  <th>解决方案</th>
                                  <th class="actions-column">操作</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($meeting['details'] as $detail): ?>
                                  <tr class="detail-item" data-detail-id="<?php echo $detail['id']; ?>">
                                    <td>
                                      <strong><?php echo htmlspecialchars($detail['responsible_person']); ?></strong>
                                      <?php if (!empty($detail['department'])): ?>
                                        <div class="small text-muted"><?php echo htmlspecialchars($detail['department']); ?></div>
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
                                        <div class="text-muted small margin-top-5">
                                          <i class="fa fa-calendar"></i> 计划完成:
                                          <?php echo htmlspecialchars($detail['planned_completion_time']); ?>
                                        </div>
                                      <?php endif; ?>
                                    </td>
                                    <td>
                                      <div class="flex">
                                        <button class="btn btn-sm btn-primary margin-right-5 edit-detail-btn"
                                          data-detail-id="<?php echo $detail['id']; ?>"
                                          data-meeting-id="<?php echo $meeting['id']; ?>">
                                          <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-detail-btn"
                                          data-detail-id="<?php echo $detail['id']; ?>">
                                          <i class="fa fa-trash"></i>
                                        </button>
                                      </div>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        <?php else: ?>
                          <div class="empty-details">
                            <p><i class="fa fa-info-circle margin-right-5"></i>暂无明细记录</p>
                          </div>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center" style="padding: 20px;">暂无会议记录</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- 分页控件 -->
        <div class="pagination-container">
          <div class="row">
            <div class="col-md-4 col-xs-12">
              <div class="page-info">
                <?php if ($total > 0): ?>
                  显示 <?php echo ($paged - 1) * $per_page + 1; ?> - <?php echo min($paged * $per_page, $total); ?> 条，共 <?php echo $total; ?> 条记录
                <?php else: ?>
                  暂无记录
                <?php endif; ?>
              </div>
            </div>

            <div class="col-md-4 col-xs-12 text-center">
              <nav aria-label="会议记录分页">
                <ul class="pagination">
                  <!-- 上一页 -->
                  <li class="<?php echo $paged <= 1 ? 'disabled' : ''; ?>">
                    <a href="<?php echo $paged <= 1 ? 'javascript:void(0)' : '?paged=' . ($paged - 1) . '&per_page=' . $per_page; ?>" aria-label="上一页">
                      <span aria-hidden="true">&laquo;</span>
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
                    <a href="<?php echo $paged >= $total_pages ? 'javascript:void(0)' : '?paged=' . ($paged + 1) . '&per_page=' . $per_page; ?>" aria-label="下一页">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>

            <!-- 每页显示记录数选择器 -->
            <div class="col-md-4 col-xs-12 text-right">
              <div class="form-inline">
                <div class="form-group">
                  <label class="control-label margin-right-5">每页显示:</label>
                  <select class="form-control input-sm" id="perPageSelect" style="width: auto;">
                    <option value="5" <?php echo $per_page == 5 ? 'selected' : ''; ?>>5条</option>
                    <option value="10" <?php echo $per_page == 10 ? 'selected' : ''; ?>>10条</option>
                    <option value="20" <?php echo $per_page == 20 ? 'selected' : ''; ?>>20条</option>
                    <option value="50" <?php echo $per_page == 50 ? 'selected' : ''; ?>>50条</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 明细编辑模态框 -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="detailModalLabel">会议明细</h4>
        </div>
        <div class="modal-body">
          <form id="detailForm" class="form-horizontal">
            <input type="hidden" id="detailId" name="id">
            <input type="hidden" id="minutesId" name="minutes_id">
            <input type="hidden" id="actionType" name="action" value="create">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="department" class="col-sm-4 control-label">责任部门</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="department" name="department">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="responsiblePerson" class="col-sm-4 control-label">责任人 <span class="text-danger">*</span></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="responsiblePerson" name="responsible_person" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="meetingContent" class="col-sm-2 control-label">会议内容 <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <textarea class="form-control" id="meetingContent" name="meeting_content" rows="4" required></textarea>
              </div>
            </div>

            <div class="form-group">
              <label for="solution" class="col-sm-2 control-label">解决方案 <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <textarea class="form-control" id="solution" name="solution" rows="4" required></textarea>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="plannedCompletionTime" class="col-sm-4 control-label">计划完成时间</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="plannedCompletionTime" name="planned_completion_time">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="cooperatingDepartment" class="col-sm-4 control-label">配合部门</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="cooperatingDepartment" name="cooperating_department">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="others" class="col-sm-2 control-label">其他</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="others" name="others" rows="2"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-primary" id="saveDetailBtn">保存</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 删除明细确认模态框 -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">删除确认</h4>
        </div>
        <div class="modal-body">
          <p>确定要删除这条会议明细吗？此操作不可撤销。</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">确认删除</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 删除会议确认模态框 -->
  <div class="modal fade" id="deleteMeetingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">删除会议确认</h4>
        </div>
        <div class="modal-body">
          <p class="text-danger"><i class="fa fa-exclamation-triangle margin-right-5"></i>警告：删除会议记录将同时删除所有相关明细！</p>
          <p>确定要删除这条会议记录吗？此操作不可撤销。</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteMeetingBtn">确认删除</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 使用本地JS文件 -->
  <script src="<?php echo $jsPath; ?>jquery.min.js"></script>
  <script src="<?php echo $jsPath; ?>bootstrap.min.js"></script>
  <script src="<?php echo $jsPath; ?>jquery.blockUI.js" type="text/javascript"></script>
  <script src="<?php echo $jsPath; ?>toastr.min.js" type="text/javascript"></script>
  <script src="<?php echo $jsPath; ?>tinymce.5.6.1.min.js" type="text/javascript"></script>
  <script src="<?php echo $jsPath; ?>jquery.validate.js" type="text/javascript"></script>
  <script src="<?php echo $jsPath; ?>additional-methods.js" type="text/javascript"></script>
  <script src="<?php echo $jsPath; ?>jquery-validation.init.js" type="text/javascript"></script>
  <script src="<?php echo $jsPath; ?>common.min.js" type="text/javascript"></script>

  <script>
    $(document).ready(function() {
      const detailApiUrl = '<?php echo $detailApi; ?>';
      const minuteApiUrl = '<?php echo $minuteApi; ?>';
      let currentDetailId = null;
      let currentMeetingId = null;

      // 展开/收起会议明细 - 修复点击事件
      $('.meeting-row').on('click', function(e) {
        初始化模态框// 只有在非按钮区域点击时展开
        if ($(e.target).closest('.btn, button, a, .delete-meeting-btn, .edit-detail-btn, .delete-detail-btn').length > 0) {
          return;
        }

        const meetingId = $(this).data('meeting-id');
        const detailRow = $(`#details-${meetingId}`);
        const expandIcon = $(this).find('.expand-icon');

        // 切换展开状态
        detailRow.toggle();
        expandIcon.toggleClass('rotate-icon');

        // 关闭其他已展开的明细
        if (detailRow.is(':visible')) {
          $('.meeting-detail').not(detailRow).hide();
          $('.meeting-row').not(this).find('.expand-icon').removeClass('rotate-icon');
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
              showNotification('删除失败: ' + result.message, 'error');
            }

            $('#deleteMeetingModal').modal('hide');
          },
          error: function(xhr, status, error) {
            console.error("API错误:", status, error, xhr.responseText);
            showNotification('网络错误，请稍后重试', 'error');
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
          showNotification('未找到明细数据', 'error');
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
              showNotification('删除失败: ' + result.message, 'error');
            }

            $('#deleteModal').modal('hide');
          },
          error: function(xhr, status, error) {
            console.error("API错误:", status, error, xhr.responseText);
            showNotification('网络错误，请稍后重试', 'error');
            $('#deleteModal').modal('hide');
          }
        });
      });

      // 保存明细
      $('#saveDetailBtn').on('click', function() {
        const form = document.getElementById('detailForm');

        if (!form.checkValidity()) {
          // Bootstrap 3中的表单验证
          $(form).addClass('was-validated');
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
              showNotification('操作失败: ' + result.message, 'error');
            }
          },
          error: function(xhr, status, error) {
            console.error("API错误:", status, error, xhr.responseText);
            showNotification('网络错误，请稍后重试', 'error');
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

      // 每页显示数量变化时
      $('#perPageSelect').on('change', function() {
        const perPage = $(this).val();
        window.location.href = `?paged=1&per_page=${perPage}`;
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