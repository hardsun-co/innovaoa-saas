<?php
/*
 * @Description: 
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:09:08
 * @LastEditTime: 2025-04-03 18:17:18
 * @LastEditors: pjw@hardsun
 * @FilePath: \Innovaoa\apps\Meeting-minutes\test\test.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

require_once '../../../common/common.php';
use Hs\Msdb;
use Hs\Fmproject\Minutes;
$minuteTable = new Minutes\Table();
$minuteDetail = new Minutes\Detail();
$minuteIndex = Minutes\Index::getInstance();

// hs_ve($minuteIndex->delete(['id'=>1]));
// hs_ve($minuteIndex->getItems(['id'=>1]));
// hs_ve($minuteIndex->getItem(1));

// hs_ve($minuteTable->getItems(['paged'=>1,'per_page'=>-1]));
// 会议主题	3月会议		会议主持人	叶子怡				
// 会议时间	2025.3.3		会议记录人	叶子怡				
// 参会人员	林秋月、洪高艺、周瑞、叶子怡、罗蔡丽、Chris							
// 缺席人员	无		缺席原因					
// 会议总结	"一、整体BN的询盘减少，原因可能是：1、无NEWS 2、中文BN的产品比较少；焊接产品有慢慢起来，后续周瑞关于BN的产品多翻译平衡好一组、二组的主项目；Emily找一些精准、质量高关于BN的news给子怡进行宣传。
// 二、2月Sales的询盘有增加共66个、电话8个，但与高峰期80+还有一定差距，有进步的空间，恢复80+询盘量
// 三、后续视频\\192.168.2.28\g\07产品视频\【CERADIR先进陶瓷在线】里面的视频文案可以借鉴参考
// 四、关于news，（1）业务员提供了许多news，但内容浅显（仅大纲级）专业度不足，（2）周瑞提议新增一个产品模块，可直接跳转到不同的模块查看更加详细的资料；或增加视频图片，增加News的可读性"							

// $data = [
  // 'id' => 1,
  // 'title' => '会议记录表1',
  // 'theme' => '3月会议',
  // 'host' => '叶子怡',
  // 'recorder' => '叶子怡',
  // 'date' => '2025-3-3',
//   'attendees' => '林秋月、洪高艺、周瑞、叶子怡、罗蔡丽、Chris',
//   'absentees' => '无',
//   'absentee_reason' => '',
//   'summary' => '一、整体BN的询盘减少，原因可能是：1、无NEWS 2、中文BN的产品比较少；焊接产品有慢慢起来，后续周瑞关于BN的产品多翻译平衡好一组、二组的主项目；Emily找一些精准、质量高关于BN的news给子怡进行宣传。
// 二、2月Sales的询盘有增加共66个、电话8个，但与高峰期80+还有一定差距，有进步的空间，恢复80+询盘量
// 三、后续视频\\192.168.2.28\g\07产品视频\【CERADIR先进陶瓷在线】里面的视频文案可以借鉴参考
// 四、关于news，（1）业务员提供了许多news，但内容浅显（仅大纲级）专业度不足，（2）周瑞提议新增一个产品模块，可直接跳转到不同的模块查看更加详细的资料；或增加视频图片，增加News的可读性'
// ];


// 周瑞	"1.网站规划方案制定（待完成）
// 2.中文新闻翻译（持续更新）
// 3.提议新增News产品模块（跳转详情页）或嵌入视频/图片"				"1.制定好方案后开会讨论，可行性
// 2.中文新闻翻译持续更新
// 3.后续发布的news：晶圆TTV/BOW/WARP 整合配套视频/图片"	持续性工作；本周内制定好方案并进行讨论	需Chris、Emily、邱总、Charlin集中讨论

// $data = [
//   'department' => '',
//   'responsible_person' => '叶子怡',
//   'meeting_content' => '1. Europage网站改版后需优化已上传产品
// 2. 标准品爆款视频拍摄（待办）
// 3. B2B/社媒链接同步至Chris',
//   'solution' => '1. 补充视频、关键词优化产品页面
// 2. 完成视频拍摄并发布
// 3. 推动Chris实现一键发布功能',
//   'planned_completion_time' => '持续性工作；本周内完成图片视频拍摄',
//   'cooperating_department' => '需Chris技术支持',
//   'minutes_id' => 2,
// ];
// $minuteIndex->addDetail($data);
$data = [
  'department' => '',
  'responsible_person' => '周瑞',
  'meeting_content' => '1.网站规划方案制定（待完成）
2.中文新闻翻译（持续更新）
3.提议新增News产品模块（跳转详情页）或嵌入视频/图片',
  'solution' => '1.制定好方案后开会讨论，可行性
2.中文新闻翻译持续更新
3.后续发布的news：晶圆TTV/BOW/WARP 整合配套视频/图片',
  'planned_completion_time' => '持续性工作；本周内制定好方案并进行讨论',
  'cooperating_department' => '需Chris、Emily、邱总、Charlin集中讨论',
  'minutes_id' => 2,
];
$minuteIndex->addDetail($data);

// $result = $minuteTable->create($data);
// $result = $minuteDetail->create($data);
// $result = $minuteTable->update($data);
// var_export('<br>result:<br>');
// var_export($result);
// 显示已加载扩展
// $extensions = get_loaded_extensions();
// echo "<h2>已加载的PHP扩展</h2>";
// echo "<pre>";
// foreach ($extensions as $ext) {
//     if (strpos($ext, 'sql') !== false) {
//         echo "$ext\n";
//     }
// }
// echo "</pre>";

// // 检查PDO驱动
// echo "<h2>PDO可用驱动</h2>";
// echo "<pre>";
// print_r(PDO::getAvailableDrivers());
// echo "</pre>";

// // 服务器信息
// phpinfo();
// $msdb = Msdb\Index::getInstance();

// var_dump($msdb->tableExists('TAB1'));
// ?>
