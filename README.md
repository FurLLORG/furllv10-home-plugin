# FurllHome — FurLL 官网首页配置插件

> **AI 辅助声明**：本插件部分代码由 AI 辅助生成 / AI 自动补全（含代码、接口文档与后台页面）。已在人工审阅与测试后发布，但请在使用前自行评估与验证。

为 **FurLLV10**（魔方业务系统 V10）官网首页提供内容配置的魔方 addon 插件：轮播图、推荐产品、合作伙伴 Logo，无需改前端代码即可后台维护官网首屏。同时融合了会员中心首页所需的**账单月度统计**与**已安装扩展列表**两个接口（原独立 `rtapi/` 接口已并入本插件）。

> 配套模板仓库：FurLLORG/furllv10-template（React 单应用官网 + 会员中心模板）。前端 `useFurllHome()` 调用本插件 `/console/v1/furll_home/home`，配置为空时自动回退内置静态数据。

## 功能特性

- **轮播图**：标题 / 导航标签 / 介绍 / 背景图 / 跳转链接 / 按钮文本，最多显示 4 个，支持排序与隐藏。
- **推荐产品**：名称 / 商品ID / 介绍 / 徽章 / 价格 / 单位 / 跳转链接，带全局开关 `recommend_enabled`，最多显示 4 个。
- **合作伙伴 Logo**：名称 / Logo / 跳转链接，按滚动行（`wall` 1/2）分两行展示，无数量限制。
- **账单月度统计**：最近 12 个月已支付 / 未支付订单金额按月汇总。
- **已安装扩展列表**：当前系统已启用的 addon 插件清单。

## 环境要求

- IDCsmart 魔方业务系统 V10（ThinkPHP 6.0.12LTS）
- PHP >= 7.2.5（需 ionCube、fileinfo 扩展）
- MySQL 5.6+

## 安装

1. 将整个 `furll_home/` 目录复制到系统插件目录：

   ```
   public/plugins/addon/furll_home/
   ```

2. 登录后台 → **插件管理** → 找到「FurLL 官网首页配置」→ 点击 **安装**（安装时自动建表并写入默认数据）。

3. 进入插件后台页面 `/admin/plugin/furll_home/index.htm` 维护轮播图、推荐产品、合作伙伴。

4. 确认官网模板使用 FurLLV10（后台配置 `web_theme = FurLLV10`），首页对应区块即读取本插件配置。

> 卸载插件将删除全部相关数据表，请谨慎操作。

## 目录结构

```
furll_home/
├── FurllHome.php               # 插件入口（安装/卸载/升级 + 默认数据）
├── route.php                   # 前台 /console/v1 与后台 /admin/v1 路由
├── auth.php                    # 后台权限定义
├── common.php                  # 公共函数
├── api.md                      # 详细接口文档
├── config/config.php           # 文件上传目录配置
├── controller/
│   ├── AdminIndexController.php    # 后台配置管理
│   └── clientarea/IndexController.php  # 前台 home / bill_monthly / addons
├── lang/                       # zh-cn / zh-hk / en-us 语言包
├── model/                      # Banner / Recommend / Partner / Config 模型
├── validate/                   # 参数校验
└── template/admin/             # 后台管理页面（TDesign 三 tab）
```

## 数据表

| 表名（框架自动加 `idcsmart_` 前缀） | 说明 |
|--------------------------------|------|
| `addon_furll_home_banner`      | 官网首页轮播图 |
| `addon_furll_home_recommend`   | 官网首页推荐产品 |
| `addon_furll_home_partner`     | 官网首页合作伙伴 Logo |
| `addon_furll_home_config`      | 全局配置（`recommend_enabled`） |

## 主要接口

### 前台 `/console/v1`（官网渲染）

| 方法 | 路径 | 认证 | 说明 |
|------|------|------|------|
| GET | `/console/v1/furll_home/home` | 无需 | 轮播图 + 推荐开关/产品 + 合作伙伴 |
| POST | `/console/v1/furll_home/bill_monthly` | 需登录 | 最近 12 个月账单金额统计 |
| GET | `/console/v1/furll_home/addons` | 无需 | 已启用插件列表 |

### 后台 `/admin/v1/furll_home`（配置管理，需后台 JWT + 权限）

| 资源 | 列表 | 详情 | 新增 | 修改 | 删除 |
|------|------|------|------|------|------|
| 轮播图 | GET `/banner` | GET `/banner/:id` | POST `/banner` | PUT `/banner/:id` | DELETE `/banner/:id` |
| 推荐产品 | GET `/recommend` | GET `/recommend/:id` | POST `/recommend` | PUT `/recommend/:id` | DELETE `/recommend/:id` |
| 合作伙伴 | GET `/partner` | GET `/partner/:id` | POST `/partner` | PUT `/partner/:id` | DELETE `/partner/:id` |
| 配置 | — | GET `/config` | — | PUT `/config` | — |

**字段通用约定**：`sort` 排序（`>=0`，默认 0）；`hidden` 显示/隐藏（`0`=显示，`1`=隐藏）。轮播图、推荐产品「显示状态」最多 4 个，超出返回 `400 最多可显示4个...`。

完整请求/响应示例见 **[api.md](api.md)**。

## 常见问题

- **前台拿不到配置？** 确认插件已安装且启用；`/console/v1/furll_home/home` 返回为空时，前端 `useFurllHome()` 会自动回退内置静态数据，属预期行为。
- **图片放哪？** 支持站内相对路径（如 `/images/home/banners/xxx.jpg`，由 FurLLV10 构建脚本复制到 `public/images/`）或插件上传目录 `/plugins/addon/furll_home/upload/`。
- **改后台要配权限吗？** 在 权限管理 中为管理员勾选「官网首页配置」下的查看 / 轮播图 / 推荐产品 / 合作伙伴 权限。

## 许可证

MIT © 2026 FurLLCN（官网 [furll.cn](https://furll.cn)）
