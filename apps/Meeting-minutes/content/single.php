<?php
/*
 * @Description: 会议记录编辑页面
 * @Author: pjw@hardsun
 * @Date: 2025-04-07 08:38:43
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */
require_once dirname(__DIR__, 3) . '/common/common.php';

use Hs\Fmproject\Minutes;

$minuteIndex = Minutes\Index::getInstance();

// 检查是否有会议ID参数
if (!empty($_GET['meeting_id']) && is_numeric($_GET['meeting_id']) && $_GET['meeting_id'] > 0) {
  $meeting_id = $_GET['meeting_id'];
  $minuteItem = $minuteIndex->getItem($meeting_id);
  if (empty($minuteItem)) {
    $meeting_id = 0;
    $minuteItem = [];
  }
} else {
  $meeting_id = 0;
  $minuteItem = [];
}

$detailApi = FM_PERFIX_PATH . 'hsapp/apps/meeting-minutes/api/detail.php';
$minuteApi = FM_PERFIX_PATH . 'hsapp/apps/meeting-minutes/api/minute.php';
$pageUpdateApi = FM_PERFIX_PATH . 'hsapp/apps/meeting-minutes/content/update/index.php';
$pageListApi = FM_PERFIX_PATH . 'hsapp/apps/meeting-minutes/content/list/index.php';
$pageAction = $meeting_id > 0 ? '编辑' : '新建';

require_once 'inc/header.php';
?>

<body>
  <div class="hs-container-fluid hs-wrapper" style="margin-top: 20px; margin-bottom: 30px;">
    <div class="hs-header-row">
      <div class="hs-header-title">
        <h2><?php echo $pageAction; ?>会议记录</h2>
      </div>
      <div class="hs-header-actions">
        <a href="<?php echo $pageListApi; ?>" class="hs-btn hs-btn-default">
          <i class="fa fa-arrow-left hs-margin-right-5"></i> 返回列表
        </a>
      </div>
    </div>

    <div class="hs-panel hs-panel-default">
      <div class="hs-panel-heading hs-bg-primary" style="background-color: #337ab7; color: white;">
        <h4 class="hs-panel-title">会议基本信息</h4>
      </div>
      <div class="hs-panel-body">
        <form id="meetingForm">
          <input type="hidden" id="meetingId" name="id" value="<?php echo $meeting_id; ?>">

          <div class="hs-row hs-margin-bottom-15">
            <div class="hs-col-md-6">
              <div class="hs-form-group hs-row">
                <label for="title" class="hs-control-label hs-required">会议标题</label>
                <input type="text" class="hs-form-control" id="title" name="title" required
                  value="<?php echo htmlspecialchars($minuteItem['title'] ?? ''); ?>">
              </div>
            </div>
            <div class="hs-col-md-6">
              <div class="hs-form-group hs-row">
                <label for="theme" class="hs-control-label hs-required">会议主题</label>
                <input type="text" class="hs-form-control" id="theme" name="theme" required
                  value="<?php echo htmlspecialchars($minuteItem['theme'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <div class="hs-row hs-margin-bottom-15">
            <div class="hs-col-md-4">
              <div class="hs-form-group hs-row">
                <label for="date" class="hs-control-label hs-required">会议日期</label>
                <input type="date" class="hs-form-control" id="date" name="date" required
                  value="<?php echo !empty($minuteItem['date']) ? date('Y-m-d', strtotime($minuteItem['date'])) : ''; ?>">
              </div>
            </div>
            <div class="hs-col-md-4">
              <div class="hs-form-group hs-row">
                <label for="host" class="hs-control-label hs-required">会议主持人</label>
                <input type="text" class="hs-form-control" id="host" name="host" required
                  value="<?php echo htmlspecialchars($minuteItem['host'] ?? ''); ?>">
              </div>
            </div>
            <div class="hs-col-md-4">
              <div class="hs-form-group hs-row">
                <label for="recorder" class="hs-control-label hs-required">会议记录人</label>
                <input type="text" class="hs-form-control" id="recorder" name="recorder" required
                  value="<?php echo htmlspecialchars($minuteItem['recorder'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <div class="hs-row hs-margin-bottom-15">
            <div class="hs-col-md-6">
              <div class="hs-form-group hs-row">
                <label for="participants" class="hs-control-label">参会人员</label>
                <textarea class="hs-form-control" id="participants" name="participants" rows="2"><?php echo htmlspecialchars($minuteItem['participants'] ?? ''); ?></textarea>
              </div>
            </div>
            <div class="hs-col-md-3">
              <div class="hs-form-group hs-row">
                <label for="absentees" class="hs-control-label">缺席人员</label>
                <input type="text" class="hs-form-control" id="absentees" name="absentees"
                  value="<?php echo htmlspecialchars($minuteItem['absentees'] ?? ''); ?>">
              </div>
            </div>
            <div class="hs-col-md-3">
              <div class="hs-form-group hs-row">
                <label for="absenceReason" class="hs-control-label">缺席原因</label>
                <input type="text" class="hs-form-control" id="absenceReason" name="absence_reason"
                  value="<?php echo htmlspecialchars($minuteItem['absence_reason'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <div class="hs-form-group hs-row">
            <label for="summary" class="hs-control-label hs-required">会议总结</label>
            <textarea class="hs-form-control hs-summary-textarea" id="summary" name="summary" rows="6" required><?php echo htmlspecialchars($minuteItem['summary'] ?? ''); ?></textarea>
          </div>

          <div class="hs-text-center">
            <button type="submit" class="hs-btn hs-btn-primary" id="saveMeetingBtn" style="padding: 8px 20px;">
              <i class="fa fa-save hs-margin-right-5"></i> 保存会议信息
            </button>
          </div>
        </form>
      </div>
    </div>

    <?php if ($meeting_id > 0): ?>
      <div class="hs-panel hs-panel-default hs-margin-top-15">
        <div class="hs-panel-heading hs-bg-primary" style="background-color: #337ab7; color: white; display: flex; justify-content: space-between; align-items: center;">
          <h4 class="hs-panel-title" style="margin: 0;">会议明细</h4>
          <button class="hs-btn hs-btn-md hs-btn-light" id="addDetailBtn" style="background-color: white; color: #337ab7;">
            <i class="fa fa-plus hs-margin-right-5"></i> 添加明细
          </button>
        </div>
        <div class="hs-panel-body">
          <div id="detailsList">
            <?php if (!empty($minuteItem['details'])): ?>
              <?php foreach ($minuteItem['details'] as $index => $detail): ?>
                <div class="hs-detail-card" data-detail-id="<?php echo $detail['id']; ?>">
                  <div class="hs-detail-header">
                    <div>
                      <strong>
                        <?php echo htmlspecialchars($detail['department'] ? $detail['department'] . ' - ' : ''); ?>
                        <?php echo htmlspecialchars($detail['responsible_person']); ?>
                      </strong>
                    </div>
                    <div class="hs-flex">
                      <button class="hs-btn hs-btn-md hs-btn-primary hs-margin-right-5 edit-detail-btn"
                        data-detail-id="<?php echo $detail['id']; ?>">
                        <i class="fas fa-edit"></i> 编辑
                      </button>
                      <button class="hs-btn hs-btn-md hs-btn-danger delete-detail-btn"
                        data-detail-id="<?php echo $detail['id']; ?>">
                        <i class="fas fa-trash"></i> 删除
                      </button>
                    </div>
                  </div>
                  <div class="hs-panel-body">
                    <div class="hs-row">
                      <div class="hs-col-md-6">
                        <h6>会议内容：</h6>
                        <div class="hs-detail-content hs-margin-bottom-15">
                          <?php echo nl2br(htmlspecialchars($detail['meeting_content'])); ?>
                        </div>
                      </div>
                      <div class="hs-col-md-6">
                        <h6>解决方案：</h6>
                        <div class="hs-detail-content hs-margin-bottom-15">
                          <?php echo nl2br(htmlspecialchars($detail['solution'])); ?>
                        </div>
                      </div>
                    </div>

                    <div class="hs-row hs-margin-top-5">
                      <div class="hs-col-xs-12">
                        <?php if (!empty($detail['planned_completion_time'])): ?>
                          <span class="hs-label hs-label-info hs-margin-right-5">
                            <i class="fa fa-calendar hs-margin-right-5"></i>
                            计划完成：<?php echo htmlspecialchars($detail['planned_completion_time']); ?>
                          </span>
                        <?php endif; ?>

                        <?php if (!empty($detail['cooperating_department'])): ?>
                          <span class="hs-label hs-label-default hs-margin-right-5">
                            <i class="fa fa-users hs-margin-right-5"></i>
                            配合部门：<?php echo htmlspecialchars($detail['cooperating_department']); ?>
                          </span>
                        <?php endif; ?>

                        <?php if (!empty($detail['others'])): ?>
                          <span class="hs-label hs-label-default">
                            <i class="fa fa-info-circle hs-margin-right-5"></i>
                            其他：<?php echo htmlspecialchars($detail['others']); ?>
                          </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="hs-empty-details">
                <i class="fa fa-info-circle hs-margin-right-5"></i> 暂无会议明细，请点击"添加明细"创建
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="hs-alert hs-alert-info hs-margin-top-15">
        <i class="fa fa-info-circle hs-margin-right-5"></i> 请先保存会议基本信息，然后才能添加会议明细
      </div>
    <?php endif; ?>
  </div>

  <!-- 明细编辑模态框 -->
  <div class="hs-modal modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="hs-modal-dialog modal-dialog modal-lg hs-modal-lg" role="document">
      <div class="modal-content hs-modal-content hs-modal-form">
        <div class="hs-modal-header">
          <h4 class="hs-modal-title" id="detailModalLabel">会议明细</h4>
          <button type="button" class="hs-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="hs-modal-body">
          <form id="detailForm" class="hs-form-horizontal">
            <input type="hidden" id="detailId" name="id">
            <input type="hidden" id="minutesId" name="minutes_id" value="<?php echo $meeting_id; ?>">
            <input type="hidden" id="actionType" name="action" value="create">

            <div class="hs-row">
              <div class="hs-col-md-6">
                <div class="hs-form-group hs-row">
                  <label for="department" class="hs-col-md-4 hs-control-label">责任部门</label>
                  <div class="hs-col-md-8">
                    <input type="text" class="hs-form-control" id="department" name="department">
                  </div>
                </div>
              </div>
              <div class="hs-col-md-6">
                <div class="hs-form-group hs-row">
                  <label for="responsiblePerson" class="hs-col-md-4 hs-control-label">责任人 <span class="hs-text-danger">*</span></label>
                  <div class="hs-col-md-8">
                    <input type="text" class="hs-form-control" id="responsiblePerson" name="responsible_person" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="hs-form-group hs-row">
              <label for="meetingContent" class="hs-col-md-2 hs-control-label">会议内容 <span class="hs-text-danger">*</span></label>
              <div class="hs-col-md-10">
                <textarea class="hs-form-control" id="meetingContent" name="meeting_content" rows="4" required></textarea>
                <div class="hs-mdall hs-text-muted">可输入多行，例如：1. 第一项内容 2. 第二项内容</div>
              </div>
            </div>

            <div class="hs-form-group hs-row">
              <label for="solution" class="hs-col-md-2 hs-control-label">解决方案 <span class="hs-text-danger">*</span></label>
              <div class="hs-col-md-10">
                <textarea class="hs-form-control" id="solution" name="solution" rows="4" required></textarea>
              </div>
            </div>

            <div class="hs-row">
              <div class="hs-col-md-6">
                <div class="hs-form-group hs-row">
                  <label for="plannedCompletionTime" class="hs-col-md-4 hs-control-label">计划完成时间</label>
                  <div class="hs-col-md-8">
                    <input type="text" class="hs-form-control" id="plannedCompletionTime" name="planned_completion_time">
                  </div>
                </div>
              </div>
              <div class="hs-col-md-6">
                <div class="hs-form-group hs-row">
                  <label for="cooperatingDepartment" class="hs-col-md-4 hs-control-label">配合部门</label>
                  <div class="hs-col-md-8">
                    <input type="text" class="hs-form-control" id="cooperatingDepartment" name="cooperating_department">
                  </div>
                </div>
              </div>
            </div>

            <div class="hs-form-group hs-row">
              <label for="others" class="hs-col-md-2 hs-control-label">其他</label>
              <div class="hs-col-md-10">
                <textarea class="hs-form-control" id="others" name="others" rows="2"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="hs-modal-footer modal-footer">
          <button type="button" class="hs-btn hs-btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="hs-btn hs-btn-primary" id="saveDetailBtn">保存</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 删除确认模态框 -->
  <div class="hs-modal modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="hs-modal-dialog modal-dialog modal-lg hs-modal-lg" role="document">
      <div class="modal-content hs-modal-content hs-modal-form">
        <div class="hs-modal-header modal-header">
          <h4 class="hs-modal-title modal-title">删除确认</h4>
          <button type="button" class="hs-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="hs-modal-body">
          <p>确定要删除这条会议明细吗？此操作不可撤销。</p>
        </div>
        <div class="hs-modal-footer modal-footer">
          <button type="button" class="hs-btn hs-btn-default" data-dismiss="modal">取消</button>
          <button type="button" class="hs-btn hs-btn-danger" id="confirmDeleteBtn">确认删除</button>
        </div>
      </div>
    </div>
  </div>

  <?php
  // 引入脚本和样式
  require_once 'inc/js-common.php';
  ?>

  <script>
    $(document).ready(function() {
      const detailApiUrl = '<?php echo $detailApi; ?>';
      let currentDetailId = null;

      // 会议表单提交
      $('#meetingForm').on('submit', function(e) {
        e.preventDefault();

        // 验证表单
        let isValid = true;

        // 清除之前的验证状态
        $('.hs-form-group').removeClass('has-error');

        // 改进的验证逻辑
        const requiredFields = {
          'title': '会议标题',
          'theme': '会议主题',
          'date': '会议日期',
          'host': '会议主持人',
          'recorder': '会议记录人',
          'summary': '会议总结'
        };

        // 直接检查每个必填字段
        for (const [fieldId, fieldName] of Object.entries(requiredFields)) {
          const $field = $('#' + fieldId);
          const value = $field.val();

          if (!value || value.trim() === '') {
            $field.closest('.hs-form-group').addClass('has-error');
            showNotification(`请填写${fieldName}`, 'warning');
            isValid = false;
            break; // 找到一个错误就停止
          }
        }

        if (!isValid) {
          return;
        }

        // 收集会议表单数据
        const formData = {};
        $(this).serializeArray().forEach(item => {
          formData[item.name] = item.value;
        });

        // 如果是编辑，则发送update请求，否则发送create请求
        formData.action = $('#meetingId').val() > 0 ? 'update' : 'create';

        // 处理日期格式 (yyyy-mm-dd -> yyyy-mm-dd 00:00:00)
        if (formData.date) {
          formData.date = formData.date + ' 00:00:00';
        }

        $.ajax({
          url: '<?php echo $minuteApi; ?>',
          type: 'POST',
          data: formData,
          success: function(response) {
            const result = typeof response === 'object' ? response : JSON.parse(response);

            if (result.code === 0) {
              // 操作成功
              showNotification('会议信息保存成功', 'success');

              // 如果是新建，则重定向到编辑页面
              if (formData.action === 'create') {
                if (result.data && result.data.id) {
                  setTimeout(() => {
                    window.location.href = `<?php echo $pageUpdateApi; ?>?meeting_id=${result.data.id}`;
                  }, 1000);
                } else if (result.id) {
                  setTimeout(() => {
                    window.location.href = `<?php echo $pageUpdateApi; ?>?meeting_id=${result.id}`;
                  }, 1000);
                }
              } else if (formData.action === 'update') {
                // 如果是更新，仅显示成功消息即可
                setTimeout(() => {
                  location.reload();
                }, 1000);
              }
            } else {
              // 操作失败
              showNotification('操作失败: ' + result.message, 'danger');
            }
          },
          error: function() {
            showNotification('网络错误，请稍后重试', 'danger');
          }
        });
      });

      // 添加明细按钮点击事件
      $('#addDetailBtn').on('click', function() {
        // 重置表单
        $('#detailForm')[0].reset();
        $('#detailId').val('');
        $('#minutesId').val(<?php echo $meeting_id; ?>);
        $('#actionType').val('create');
        $('#detailModalLabel').text('添加会议明细');

        $('#detailModal').modal('show');
      });

      // 编辑明细按钮点击事件
      $(document).on('click', '.edit-detail-btn', function() {
        const detailId = $(this).data('detail-id');
        currentDetailId = detailId;

        // 找到当前明细数据
        const detailData = <?php echo json_encode($minuteItem['details'] ?? []); ?>;
        const detail = detailData.find(item => item.id == detailId);

        if (detail) {
          // 填充表单数据
          $('#detailId').val(detail.id);
          $('#minutesId').val(detail.minutes_id);
          $('#department').val(detail.department);
          $('#responsiblePerson').val(detail.responsible_person);
          $('#meetingContent').val(detail.meeting_content);
          $('#solution').val(detail.solution);
          $('#plannedCompletionTime').val(detail.planned_completion_time);
          $('#cooperatingDepartment').val(detail.cooperating_department);
          $('#others').val(detail.others);
          $('#actionType').val('update');
          $('#detailModalLabel').text('编辑会议明细');

          $('#detailModal').modal('show');
        }
      });

      // 删除明细按钮点击事件
      $(document).on('click', '.delete-detail-btn', function() {
        currentDetailId = $(this).data('detail-id');
        $('#deleteModal').modal('show');
      });

      // 保存明细
      $('#saveDetailBtn').on('click', function() {
        // 验证表单
        const form = document.getElementById('detailForm');
        let isValid = true;

        // 清除之前的验证状态
        $('.hs-form-group').removeClass('has-error');

        // 检查必填字段
        $('input[required], textarea[required]').each(function() {
          if (!$(this).val().trim()) {
            $(this).closest('.hs-form-group').addClass('has-error');
            isValid = false;
          }
        });

        if (!isValid) {
          showNotification('请填写所有必填项', 'warning');
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
          error: function() {
            showNotification('网络错误，请稍后重试', 'danger');
          }
        });
      });

      // 确认删除
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
          error: function() {
            showNotification('网络错误，请稍后重试', 'danger');
            $('#deleteModal').modal('hide');
          }
        });
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
          case 'danger':
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