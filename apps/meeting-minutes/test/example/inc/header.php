<?php
/*
 * @Description:
 * @Author: chris@hardsun.cn
 * @Date: 2025-04-08 16:54:05
 * @LastEditTime: 2025-04-08 16:57:37
 * @LastEditors: chris@hardsun.cn
 * @File: /Users/chris/Dropbox/projects/github/innovaoa-saas/apps/meeting-minutes/test/example/inc/header.php
 * @Copyright: Copyright©2019-2025 HARDSUN & CERADIR
 */
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


  <!-- <link href="https://hardsun.cn/assets/common/css/main.min.css" rel="stylesheet" type="text/css"> -->


  <!-- oa feedback form css -->
  <!-- <link href="https://2innova.hardsun.cn/tools/oa-feedback/static/css/main.min.css" rel="stylesheet" type="text/css" /> -->



  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <!-- Font Awesome 图标库 -->
  <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
    .hs-col-md-4,
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
      .hs-col-md-4 {
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
      max-width: 900px;
      /* 根据需要调整 */
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
      min-height: 16.42px;
      /* 确保有足够高度容纳关闭按钮 */
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
      margin-right: 10px;
      /* 增加边距 */
    }

    /* 优化箭头图标显示 */
    .hs-expand-icon {
      font-size: 14px;
      /* 适当调整图标大小 */
      width: 16px;
      /* 固定宽度 */
      text-align: center;
      /* 居中对齐 */
      color: #337ab7;
      /* 使用主色调 */
    }

    /* 改进会议记录主题和标题样式 */
    .hs-meeting-title {
      font-weight: 500;
      margin-left: 2px;
      /* 微调左边距 */
    }
  </style>
</head>

<body>