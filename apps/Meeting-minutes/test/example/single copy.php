<?php
/*
 * @Description: 会议记录编辑页面
 * @Author: pjw@hardsun
 * @Date: 2025-04-07 08:38:43
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */
require_once '../../../../common/common.php';
use Hs\Fmproject\Minutes;
$minuteIndex = Minutes\Index::getInstance();

// 检查是否有会议ID参数
if(!empty($_GET['meeting_id']) && is_numeric($_GET['meeting_id']) && $_GET['meeting_id'] > 0){
  $meeting_id = $_GET['meeting_id'];
  $minuteItem = $minuteIndex->getItem($meeting_id);
  if(empty($minuteItem)){
    $meeting_id = 0;
    $minuteItem = [];
  }
} else {
  $meeting_id = 0;
  $minuteItem = [];
}

// $detailApi = MAIN_DOMAIN_COMMON.'/apps/meeting-minutes/test/example/detailapi.php';
// $minuteApi = MAIN_DOMAIN_COMMON.'/apps/meeting-minutes/test/example/minuteapi.php';
$detailApi = 'http://innovaoaloc.cn/apps/Meeting-minutes/test/example/detailapi.php';
$minuteApi = 'http://innovaoaloc.cn/apps/Meeting-minutes/test/example/minuteapi.php';
$pageAction = $meeting_id > 0 ? '编辑' : '新建';
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageAction; ?>会议记录</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .detail-card {
            border-left: 3px solid #0d6efd;
            margin-bottom: 1rem;
        }
        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }
        .summary-textarea {
            min-height: 150px;
        }
        .detail-content {
            white-space: pre-line;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .detail-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .empty-details {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            border: 1px dashed #dee2e6;
            border-radius: 5px;
            margin: 15px 0;
        }
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?php echo $pageAction; ?>会议记录</h2>
            <a href="lists.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> 返回列表
            </a>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">会议基本信息</h5>
            </div>
            <div class="card-body">
                <form id="meetingForm">
                    <input type="hidden" id="meetingId" name="id" value="<?php echo $meeting_id; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label required-field">会议标题</label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="<?php echo htmlspecialchars($minuteItem['title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="theme" class="form-label required-field">会议主题</label>
                            <input type="text" class="form-control" id="theme" name="theme" required
                                   value="<?php echo htmlspecialchars($minuteItem['theme'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label required-field">会议日期</label>
                            <input type="date" class="form-control" id="date" name="date" required
                                   value="<?php echo !empty($minuteItem['date']) ? date('Y-m-d', strtotime($minuteItem['date'])) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="host" class="form-label required-field">会议主持人</label>
                            <input type="text" class="form-control" id="host" name="host" required
                                   value="<?php echo htmlspecialchars($minuteItem['host'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="recorder" class="form-label required-field">会议记录人</label>
                            <input type="text" class="form-control" id="recorder" name="recorder" required
                                   value="<?php echo htmlspecialchars($minuteItem['recorder'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="participants" class="form-label">参会人员</label>
                            <textarea class="form-control" id="participants" name="participants" rows="2"><?php echo htmlspecialchars($minuteItem['participants'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="absentees" class="form-label">缺席人员</label>
                            <input type="text" class="form-control" id="absentees" name="absentees"
                                   value="<?php echo htmlspecialchars($minuteItem['absentees'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="absenceReason" class="form-label">缺席原因</label>
                            <input type="text" class="form-control" id="absenceReason" name="absence_reason"
                                   value="<?php echo htmlspecialchars($minuteItem['absence_reason'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="summary" class="form-label required-field">会议总结</label>
                        <textarea class="form-control summary-textarea" id="summary" name="summary" rows="6" required><?php echo htmlspecialchars($minuteItem['summary'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-4 py-2" id="saveMeetingBtn">
                            <i class="fas fa-save me-2"></i> 保存会议信息
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if($meeting_id > 0): ?>
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">会议明细</h5>
                <button class="btn btn-light btn-sm" id="addDetailBtn">
                    <i class="fas fa-plus me-1"></i> 添加明细
                </button>
            </div>
            <div class="card-body">
                <div id="detailsList">
                    <?php if(!empty($minuteItem['details'])): ?>
                        <?php foreach($minuteItem['details'] as $index => $detail): ?>
                            <div class="card detail-card mb-3" data-detail-id="<?php echo $detail['id']; ?>">
                                <div class="detail-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>
                                            <?php echo htmlspecialchars($detail['department'] ? $detail['department'] . ' - ' : ''); ?>
                                            <?php echo htmlspecialchars($detail['responsible_person']); ?>
                                        </strong>
                                    </div>
                                    <div class="action-buttons">
                                        <button class="btn btn-outline-primary btn-sm edit-detail-btn" 
                                                data-detail-id="<?php echo $detail['id']; ?>">
                                            <i class="fas fa-edit"></i> 编辑
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm delete-detail-btn" 
                                                data-detail-id="<?php echo $detail['id']; ?>">
                                            <i class="fas fa-trash"></i> 删除
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>会议内容：</h6>
                                            <div class="detail-content mb-3">
                                                <?php echo nl2br(htmlspecialchars($detail['meeting_content'])); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>解决方案：</h6>
                                            <div class="detail-content mb-3">
                                                <?php echo nl2br(htmlspecialchars($detail['solution'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <?php if(!empty($detail['planned_completion_time'])): ?>
                                                <span class="badge bg-info me-2">
                                                    <i class="far fa-calendar-alt me-1"></i> 
                                                    计划完成：<?php echo htmlspecialchars($detail['planned_completion_time']); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($detail['cooperating_department'])): ?>
                                                <span class="badge bg-secondary me-2">
                                                    <i class="fas fa-users me-1"></i> 
                                                    配合部门：<?php echo htmlspecialchars($detail['cooperating_department']); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($detail['others'])): ?>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-info-circle me-1"></i> 
                                                    其他：<?php echo htmlspecialchars($detail['others']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-details">
                            <i class="fas fa-info-circle me-2"></i> 暂无会议明细，请点击"添加明细"创建
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i> 请先保存会议基本信息，然后才能添加会议明细
            </div>
        <?php endif; ?>
    </div>

    <!-- 明细编辑模态框 -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">会议明细</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="detailForm">
                        <input type="hidden" id="detailId" name="id">
                        <input type="hidden" id="minutesId" name="minutes_id" value="<?php echo $meeting_id; ?>">
                        <input type="hidden" id="actionType" name="action" value="create">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="department" class="form-label">责任部门</label>
                                <input type="text" class="form-control" id="department" name="department">
                            </div>
                            <div class="col-md-6">
                                <label for="responsiblePerson" class="form-label required-field">责任人</label>
                                <input type="text" class="form-control" id="responsiblePerson" name="responsible_person" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meetingContent" class="form-label required-field">会议内容</label>
                            <textarea class="form-control" id="meetingContent" name="meeting_content" rows="4" required></textarea>
                            <div class="form-text">可输入多行，例如：1. 第一项内容 2. 第二项内容</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="solution" class="form-label required-field">解决方案</label>
                            <textarea class="form-control" id="solution" name="solution" rows="4" required></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="plannedCompletionTime" class="form-label">计划完成时间</label>
                                <input type="text" class="form-control" id="plannedCompletionTime" name="planned_completion_time">
                            </div>
                            <div class="col-md-6">
                                <label for="cooperatingDepartment" class="form-label">配合部门</label>
                                <input type="text" class="form-control" id="cooperatingDepartment" name="cooperating_department">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="others" class="form-label">其他</label>
                            <textarea class="form-control" id="others" name="others" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" id="saveDetailBtn">保存</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 删除确认模态框 -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">删除确认</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>确定要删除这条会议明细吗？此操作不可撤销。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">确认删除</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 提示弹窗容器 -->
    <div class="toast-container"></div>

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            const detailApiUrl = '<?php echo $detailApi; ?>';
            let currentDetailId = null;
            
            // 初始化模态框
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            // 会议表单提交
            $('#meetingForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
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
                    url: '<?php echo $minuteApi; ?>', // 修正：使用会议 API
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        const result = typeof response === 'object' ? response : JSON.parse(response);
                        
                        if (result.code === 0) {
                            // 操作成功
                            showToast('会议信息保存成功', 'success');
                            
                            // 如果是新建，则重定向到编辑页面
                            if (formData.action === 'create') {
                                if (result.data && result.data.id) {
                                    // 修正：使用正确的数据路径
                                    setTimeout(() => {
                                        window.location.href = `single.php?meeting_id=${result.data.id}`;
                                    }, 1000);
                                } else if (result.id) {
                                    // 兼容旧格式
                                    setTimeout(() => {
                                        window.location.href = `single.php?meeting_id=${result.id}`;
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
                            showToast('操作失败: ' + result.message, 'danger');
                        }
                    },
                    error: function() {
                        showToast('网络错误，请稍后重试', 'danger');
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
                
                detailModal.show();
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
                    
                    detailModal.show();
                }
            });
            
            // 删除明细按钮点击事件
            $(document).on('click', '.delete-detail-btn', function() {
                currentDetailId = $(this).data('detail-id');
                deleteModal.show();
            });
            
            // 保存明细
            $('#saveDetailBtn').on('click', function() {
                const form = document.getElementById('detailForm');
                
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
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
                            showToast(actionType === 'create' ? '明细添加成功' : '明细更新成功', 'success');
                            
                            // 关闭模态框并刷新页面
                            detailModal.hide();
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            // 操作失败
                            showToast('操作失败: ' + result.message, 'danger');
                        }
                    },
                    error: function() {
                        showToast('网络错误，请稍后重试', 'danger');
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
                            showToast('明细删除成功', 'success');
                            
                            // 刷新页面
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            // 删除失败
                            showToast('删除失败: ' + result.message, 'danger');
                        }
                        
                        deleteModal.hide();
                    },
                    error: function() {
                        showToast('网络错误，请稍后重试', 'danger');
                        deleteModal.hide();
                    }
                });
            });
            
            // 显示提示消息
            function showToast(message, type = 'success') {
                const toastId = 'toast-' + Date.now();
                const toast = `
                    <div id="${toastId}" class="toast bg-${type} text-white" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-${type} text-white">
                            <strong class="me-auto">系统提示</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            ${message}
                        </div>
                    </div>
                `;
                
                $('.toast-container').append(toast);
                const toastElement = new bootstrap.Toast(document.getElementById(toastId), {
                    delay: 3000
                });
                toastElement.show();
                
                // 自动移除已关闭的提示
                $(`#${toastId}`).on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }
        });
    </script>
</body>
</html>