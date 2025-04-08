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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .badge {
            font-size: 85%;
        }
        /* 分页样式增强 */
        .pagination {
            margin-bottom: 0;
        }
        .page-info {
            color: #6c757d;
        }
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .pagination-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>会议记录管理</h2>
            <a href="single.php" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> 新建会议
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
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
                                    <tr class="meeting-row" data-meeting-id="<?php echo $meeting['id']; ?>">
                                        <td><?php echo $meeting['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-chevron-down expand-icon me-2"></i>
                                                <div>
                                                    <span class="meeting-title"><?php echo htmlspecialchars($meeting['theme']); ?></span>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($meeting['title']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="meeting-date"><?php echo date('Y-m-d', strtotime($meeting['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($meeting['host']); ?></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="single.php?meeting_id=<?php echo $meeting['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="编辑">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger delete-meeting-btn" data-meeting-id="<?php echo $meeting['id']; ?>" title="删除">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo count($meeting['details']); ?>项
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- 会议明细展开区域 -->
                                    <tr class="meeting-detail" id="details-<?php echo $meeting['id']; ?>">
                                        <td colspan="6" class="p-0">
                                            <div class="detail-container">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <h5>会议明细</h5>
                                                    <button class="btn btn-sm btn-success add-detail-btn" 
                                                            data-meeting-id="<?php echo $meeting['id']; ?>">
                                                        <i class="fas fa-plus"></i> 添加明细
                                                    </button>
                                                </div>
                                                
                                                <!-- 会议摘要信息 -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light">
                                                        会议信息
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <p><strong>记录人：</strong> <?php echo htmlspecialchars($meeting['recorder']); ?></p>
                                                                <p><strong>参会人员：</strong> <?php echo !empty($meeting['participants']) ? htmlspecialchars($meeting['participants']) : '无记录'; ?></p>
                                                                <p><strong>缺席人员：</strong> <?php echo !empty($meeting['absentees']) ? htmlspecialchars($meeting['absentees']) : '无'; ?></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>会议总结：</strong></p>
                                                                <div class="border p-2 rounded bg-white" style="max-height:150px;overflow-y:auto">
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
                                                            <thead class="table-light">
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
                                                                                <div class="text-muted small mt-1">
                                                                                    <i class="fas fa-calendar-alt"></i> 计划完成: 
                                                                                    <?php echo htmlspecialchars($detail['planned_completion_time']); ?>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex">
                                                                                <button class="btn btn-sm btn-outline-primary me-1 edit-detail-btn" 
                                                                                        data-detail-id="<?php echo $detail['id']; ?>"
                                                                                        data-meeting-id="<?php echo $meeting['id']; ?>">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </button>
                                                                                <button class="btn btn-sm btn-outline-danger delete-detail-btn"
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
                                                    <div class="empty-details">
                                                        <p><i class="fas fa-info-circle me-2"></i>暂无明细记录</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">暂无会议记录</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 分页控件 - 修改条件，始终显示分页区域 -->
                <div class="pagination-container">
                    <div class="page-info">
                        <?php if($total > 0): ?>
                            显示 <?php echo ($paged - 1) * $per_page + 1; ?> - <?php echo min($paged * $per_page, $total); ?> 条，共 <?php echo $total; ?> 条记录
                        <?php else: ?>
                            暂无记录
                        <?php endif; ?>
                    </div>
                    
                    <nav aria-label="会议记录分页">
                        <ul class="pagination">
                            <!-- 上一页 -->
                            <li class="page-item <?php echo $paged <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?paged=<?php echo $paged - 1; ?>&per_page=<?php echo $per_page; ?>" aria-label="上一页">
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
                                echo '<li class="page-item"><a class="page-link" href="?paged=1&per_page=' . $per_page . '">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            // 页码
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $active = $i == $paged ? 'active' : '';
                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="?paged=' . $i . '&per_page=' . $per_page . '">' . $i . '</a></li>';
                            }
                            
                            // 最后一页
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?paged=' . $total_pages . '&per_page=' . $per_page . '">' . $total_pages . '</a></li>';
                            }
                            ?>
                            
                            <!-- 下一页 -->
                            <li class="page-item <?php echo $paged >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?paged=<?php echo $paged + 1; ?>&per_page=<?php echo $per_page; ?>" aria-label="下一页">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <!-- 每页显示记录数选择器 -->
                    <div class="d-flex align-items-center">
                        <span class="me-2">每页显示:</span>
                        <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
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
                        <input type="hidden" id="minutesId" name="minutes_id">
                        <input type="hidden" id="actionType" name="action" value="create">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="department" class="form-label">责任部门</label>
                                <input type="text" class="form-control" id="department" name="department">
                            </div>
                            <div class="col-md-6">
                                <label for="responsiblePerson" class="form-label">责任人 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="responsiblePerson" name="responsible_person" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meetingContent" class="form-label">会议内容 <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="meetingContent" name="meeting_content" rows="4" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="solution" class="form-label">解决方案 <span class="text-danger">*</span></label>
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
    
    <!-- 删除明细确认模态框 -->
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

    <!-- 删除会议确认模态框 -->
    <div class="modal fade" id="deleteMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">删除会议确认</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>警告：删除会议记录将同时删除所有相关明细！</p>
                    <p>确定要删除这条会议记录吗？此操作不可撤销。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteMeetingBtn">确认删除</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 提示框 -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="toastNotification" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            const detailApiUrl = '<?php echo $detailApi; ?>';
            const minuteApiUrl = '<?php echo $minuteApi; ?>';
            let currentDetailId = null;
            let currentMeetingId = null;
            
            // 初始化模态框
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const deleteMeetingModal = new bootstrap.Modal(document.getElementById('deleteMeetingModal'));
            const toast = new bootstrap.Toast(document.getElementById('toastNotification'));
            
            // 每页显示数量变化时
            $('#perPageSelect').on('change', function() {
                const perPage = $(this).val();
                window.location.href = `?paged=1&per_page=${perPage}`;
            });

            // 展开/收起会议明细 - 修复点击事件
            $('.meeting-row').on('click', function(e) {
                // 只有在非按钮区域点击时展开
                if($(e.target).closest('.btn, button, a, .delete-meeting-btn, .edit-detail-btn, .delete-detail-btn').length > 0) {
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
                deleteMeetingModal.show();
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
                        
                        deleteMeetingModal.hide();
                    },
                    error: function(xhr, status, error) {
                        console.error("API错误:", status, error, xhr.responseText);
                        showNotification('网络错误，请稍后重试', 'danger');
                        deleteMeetingModal.hide();
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
                    
                    detailModal.show();
                } else {
                    showNotification('未找到明细数据', 'danger');
                    console.error('未找到明细数据:', detailId, meetingId);
                }
            });

            // 删除明细按钮点击事件
            $(document).on('click', '.delete-detail-btn', function(e) {
                e.stopPropagation(); // 阻止事件冒泡
                currentDetailId = $(this).data('detail-id');
                deleteModal.show();
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
                        
                        deleteModal.hide();
                    },
                    error: function(xhr, status, error) {
                        console.error("API错误:", status, error, xhr.responseText);
                        showNotification('网络错误，请稍后重试', 'danger');
                        deleteModal.hide();
                    }
                });
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
                            showNotification(actionType === 'create' ? '明细添加成功' : '明细更新成功', 'success');
                            
                            // 关闭模态框并刷新页面
                            detailModal.hide();
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
                
                detailModal.show();
            });
            
            // 显示通知提示
            function showNotification(message, type) {
                const toastElement = $('#toastNotification');
                toastElement.removeClass('bg-success bg-danger bg-warning');
                toastElement.addClass(`bg-${type}`);
                toastElement.find('.toast-body').text(message);
                toast.show();
            }
        });
    </script>
</body>
</html>