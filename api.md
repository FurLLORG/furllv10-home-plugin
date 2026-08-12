# FurLL 官网首页配置插件 - API 文档

插件：`FurllHome` v1.1.0
说明：为 FurLLV10 官网首页提供轮播图、推荐产品、合作伙伴 Logo 的配置，内置账单月度统计与已安装扩展列表接口（原 `rtapi/` 独立接口已融合进本插件），并提供官方 `pc/default` 模板内容渲染接口供 FurLLV10 未适配页面的 iframe 嵌入。

## 通用约定

- 数据表前缀：`idcsmart_`（框架自动拼接，以下 URL 不含前缀）
- 认证：
  - 前台 `/console/v1/**`：`Authorization: Bearer <JWT>`。`home`、`default-cart-goods`、`default-product-detail`、`addons` 接口无需登录；`bill_monthly` 需登录（未登录返回 `{"status":401}`）。
  - 后台 `/admin/v1/**`：`Authorization: Bearer <JWT>`（后台 token，`localStorage.backJwt`），并校验后台权限。
- `hidden` 字段语义：`0` = 显示，`1` = 隐藏（所有资源一致）。
- 返回统一结构：`{"status":200,"msg":"请求成功","data":{...}}`；错误时 `status` 为 `400/401/404` 等，`msg` 为中文提示。
- 上限规则：轮播图、推荐产品「显示状态（hidden=0）」**最多 4 个**，超出返回 `{"status":400,"msg":"最多可显示4个..."}`；合作伙伴无数量限制。

---

## 一、前台接口 `/console/v1`（官网页面渲染）

### 1.1 首页配置

- URL：`GET /console/v1/furll_home/home`
- 认证：无需登录
- 说明：返回官网首页轮播图、推荐产品开关、推荐产品、合作伙伴 Logo。前端 `useFurllHome()` 调用，配置为空时回退内置静态数据。

请求示例：

```
GET /console/v1/furll_home/home
```

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": {
    "banners": [
      {
        "id": 1,
        "title": "高性能云服务器",
        "label": "高性能云",
        "description": "优质稳定网络，满血性能释放，高防不惧攻击",
        "image": "/images/home/banners/banner-1.png",
        "url": "",
        "button_text": "立即购买",
        "sort": 0,
        "create_time": 1786275400
      }
    ],
    "recommend_enabled": "1",
    "recommends": [
      {
        "id": 1,
        "product_id": 0,
        "name": "海外加速白银版",
        "description": "网站类",
        "tag": "限时推荐",
        "price": "4.20",
        "unit": "/ 月",
        "url": "",
        "create_time": 1786275400
      }
    ],
    "partners": [
      {
        "id": 1,
        "name": "FurTech",
        "image": "/images/home/partners/67a8c647a0e80.png",
        "url": "",
        "wall": 1,
        "create_time": 1786275400
      }
    ]
  }
}
```

字段说明：
- `banners[]`：`title` 标题、`label` 导航标签、`description` 介绍、`image` 背景图、`url` 跳转链接、`button_text` 按钮文本、`sort` 排序。
- `recommend_enabled`：推荐产品开关，`"1"` 开启、`"0"` 关闭（字符串）。
- `recommends[]`：`name` 产品名称、`description` 介绍、`tag` 徽章、`price` 价格、`unit` 价格单位、`url` 跳转链接、`product_id` 关联商品 ID。
- `partners[]`：`name` 名称、`image` Logo、`url` 跳转链接、`wall` 滚动行（`1` 第一行 / `2` 第二行）。

> 说明：前台返回的列表中已剔除 `hidden`、`sort` 字段（仅在后台可见）。

---

### 1.2 账单月度统计

- URL：`POST /console/v1/furll_home/bill_monthly`
- 认证：需登录
- 说明：返回当前登录用户最近 12 个月已支付/未支付订单金额按月汇总（原 `rtapi/bill_monthly.php` 融合）。

请求示例：

```
POST /console/v1/furll_home/bill_monthly
Authorization: Bearer <JWT>
```

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": {
    "client_id": 1,
    "months": [
      { "month": "2025-09", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2025-10", "paid": "12.50", "unpaid": "0.00" },
      { "month": "2025-11", "paid": "0.00", "unpaid": "88.00" },
      { "month": "2025-12", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-01", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-02", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-03", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-04", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-05", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-06", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-07", "paid": "0.00", "unpaid": "0.00" },
      { "month": "2026-08", "paid": "0.00", "unpaid": "0.00" }
    ]
  }
}
```

字段说明：
- `months[]`：`month` 年月（`YYYY-MM`，按时间倒序，当前月为最后一项）；`paid` 已支付金额；`unpaid` 未支付金额（均保留两位小数字符串）。
- 统计口径：已支付按 `pay_time` 归月、`status=Paid`；未支付按 `create_time` 归月、`status=Unpaid`。

未登录响应：

```json
{ "status": 401, "msg": "未登录或登录已过期" }
```

---

### 1.3 已安装扩展(addon)列表

- URL：`GET /console/v1/furll_home/addons`
- 认证：无需登录
- 说明：返回当前系统已启用（`status=1`、`module=addon`）的插件列表（原 `rtapi/addons.php` 融合）。

请求示例：

```
GET /console/v1/furll_home/addons
```

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": {
    "client_id": 1,
    "addons": [
      { "id": 1, "name": "IdcsmartTicket", "title": "工单系统", "url": "" },
      { "id": 2, "name": "FurllHome", "title": "FurLL 官网首页配置", "url": "" }
    ],
    "count": 2
  }
}
```

字段说明：
- `addons[]`：`id` 插件 ID、`name` 插件英文名（唯一标识）、`title` 显示名称、`url` 跳转地址。

---

### 1.4 官方 default 模板内容渲染

- URL：`GET /console/v1/furll_home/default-cart-goods`、`GET /console/v1/furll_home/default-product-detail`
- 认证：无需登录
- 说明：返回官方 `pc/default` 模板的真实渲染结果，供 FurLLV10 未适配页面的 iframe 嵌入。
  - `default-cart-goods` 渲染 `cart/template/pc/default/goods.php`（商品选配页）
  - `default-product-detail` 渲染 `clientarea/template/pc/default/productdetail.php`（产品详情/管理页）
  - 均注入 `<style id="furll-default-content-shell">` 隐藏官方顶栏/侧栏，避免双重导航；
    模块脚本、CSS、语言包来自官方 default 模板，与后台配置完全一致。
- **FurLLV10 未适配的产品选配 / 管理页面依赖本接口，插件必须安装；未安装时返回 404。**

请求示例：

```
GET /console/v1/furll_home/default-cart-goods?id=456&change=true&name=我的云主机
GET /console/v1/furll_home/default-product-detail?id=789
```

参数说明：
- `id`（必填）：`default-cart-goods` 为商品 ID（`productId`）；`default-product-detail` 为主机 ID（`hostId`）。
- `change`（可选，仅商品页）：是否编辑模式（官方 goods.htm?change=true）。
- `name`（可选，仅商品页）：商品名称（编辑模式显示用）。

响应：`Content-Type: text/html; charset=utf-8`，`Cache-Control: no-store`，`200` 返回完整 HTML
（含官方 `pc/default` 的 header/footer 壳与对应模块 Vue 脚本）；参数缺失返回 `400`。

---

## 二、后台接口 `/admin/v1`（配置管理）

统一前缀：`/admin/v1/furll_home/`，全部需后台 JWT + 权限认证。

通用校验参数：
- `sort`：整数 `>=0`，可选，默认 `0`
- `hidden`：`0` 显示 / `1` 隐藏，可选，默认 `0`

### 2.1 轮播图 Banner

#### 列表

- URL：`GET /admin/v1/furll_home/banner`
- 参数（Query）：`keywords` 标题关键字（可选）、`page`、`limit`、`sort`/`orderby`

请求示例：

```
GET /admin/v1/furll_home/banner?keywords=&page=1&limit=20
```

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "title": "高性能云服务器",
        "label": "高性能云",
        "description": "优质稳定网络，满血性能释放，高防不惧攻击",
        "image": "/images/home/banners/banner-1.png",
        "url": "",
        "button_text": "立即购买",
        "sort": 0,
        "hidden": 0,
        "create_time": 1786275400
      }
    ],
    "count": 1
  }
}
```

#### 详情

- URL：`GET /admin/v1/furll_home/banner/:id`

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": { "banner": { "id": 1, "title": "高性能云服务器", "hidden": 0 } }
}
```

#### 新增

- URL：`POST /admin/v1/furll_home/banner`
- 必填：`title`（≤200）
- 可选：`label`(≤100)、`description`(≤500)、`image`(≤500)、`url`(≤500)、`button_text`(≤100)、`sort`、`hidden`

请求示例（JSON Body）：

```json
{
  "title": "高性能云服务器",
  "label": "高性能云",
  "description": "优质稳定网络，满血性能释放，高防不惧攻击",
  "image": "/images/home/banners/banner-1.png",
  "url": "",
  "button_text": "立即购买",
  "sort": 0,
  "hidden": 0
}
```

响应示例：

```json
{ "status": 200, "msg": "创建成功", "data": [] }
```

#### 修改

- URL：`PUT /admin/v1/furll_home/banner/:id`
- 必填：`id`、`title`；其余同「新增」

请求示例（JSON Body）：

```json
{ "id": 1, "title": "高性能云服务器", "hidden": 1 }
```

响应示例：

```json
{ "status": 200, "msg": "修改成功", "data": [] }
```

#### 删除

- URL：`DELETE /admin/v1/furll_home/banner/:id`

响应示例：

```json
{ "status": 200, "msg": "删除成功", "data": [] }
```

---

### 2.2 推荐产品 Recommend

#### 列表

- URL：`GET /admin/v1/furll_home/recommend`
- 参数（Query）：`keywords` 名称关键字（可选）、`page`、`limit`

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "product_id": 0,
        "name": "海外加速白银版",
        "description": "网站类",
        "tag": "限时推荐",
        "price": "4.20",
        "unit": "/ 月",
        "url": "",
        "sort": 0,
        "hidden": 0,
        "create_time": 1786275400
      }
    ],
    "count": 1
  }
}
```

#### 详情

- URL：`GET /admin/v1/furll_home/recommend/:id`

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": { "recommend": { "id": 1, "name": "海外加速白银版", "hidden": 0 } }
}
```

#### 新增

- URL：`POST /admin/v1/furll_home/recommend`
- 必填：`name`（≤200）
- 可选：`product_id`(整数≥0)、`description`(≤500)、`tag`(≤100)、`price`(≤50)、`unit`(≤50)、`url`(≤500)、`sort`、`hidden`

请求示例（JSON Body）：

```json
{
  "product_id": 0,
  "name": "海外加速白银版",
  "description": "网站类",
  "tag": "限时推荐",
  "price": "4.20",
  "unit": "/ 月",
  "url": "",
  "sort": 0,
  "hidden": 0
}
```

响应示例：

```json
{ "status": 200, "msg": "创建成功", "data": [] }
```

#### 修改

- URL：`PUT /admin/v1/furll_home/recommend/:id`
- 必填：`id`、`name`；其余同「新增」

请求示例：

```json
{ "id": 1, "name": "海外加速白银版", "hidden": 1 }
```

响应示例：

```json
{ "status": 200, "msg": "修改成功", "data": [] }
```

#### 删除

- URL：`DELETE /admin/v1/furll_home/recommend/:id`

响应示例：

```json
{ "status": 200, "msg": "删除成功", "data": [] }
```

---

### 2.3 合作伙伴 Partner

#### 列表

- URL：`GET /admin/v1/furll_home/partner`
- 参数（Query）：`keywords` 名称关键字（可选）、`wall`(1/2，可选)、`page`、`limit`

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "name": "FurTech",
        "image": "/images/home/partners/67a8c647a0e80.png",
        "url": "",
        "wall": 1,
        "sort": 0,
        "hidden": 0,
        "create_time": 1786275400
      }
    ],
    "count": 1
  }
}
```

#### 详情

- URL：`GET /admin/v1/furll_home/partner/:id`

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": { "partner": { "id": 1, "name": "FurTech", "wall": 1, "hidden": 0 } }
}
```

#### 新增

- URL：`POST /admin/v1/furll_home/partner`
- 必填：`name`（≤200）
- 可选：`image`(≤500)、`url`(≤500)、`wall`(`1`/`2`，默认 `1`)、`sort`、`hidden`

请求示例（JSON Body）：

```json
{
  "name": "FurTech",
  "image": "/images/home/partners/67a8c647a0e80.png",
  "url": "",
  "wall": 1,
  "sort": 0,
  "hidden": 0
}
```

响应示例：

```json
{ "status": 200, "msg": "创建成功", "data": [] }
```

#### 修改

- URL：`PUT /admin/v1/furll_home/partner/:id`
- 必填：`id`、`name`；其余同「新增」

请求示例：

```json
{ "id": 1, "name": "FurTech", "wall": 2, "hidden": 1 }
```

响应示例：

```json
{ "status": 200, "msg": "修改成功", "data": [] }
```

#### 删除

- URL：`DELETE /admin/v1/furll_home/partner/:id`

响应示例：

```json
{ "status": 200, "msg": "删除成功", "data": [] }
```

---

### 2.4 配置 Config

#### 查询配置

- URL：`GET /admin/v1/furll_home/config`

响应示例：

```json
{
  "status": 200,
  "msg": "请求成功",
  "data": { "config": { "recommend_enabled": "1" } }
}
```

#### 保存配置

- URL：`PUT /admin/v1/furll_home/config`
- 可选：`recommend_enabled`（`0` 关闭 / `1` 开启）

请求示例（JSON Body）：

```json
{ "recommend_enabled": "1" }
```

响应示例：

```json
{ "status": 200, "msg": "修改成功", "data": [] }
```

---

## 三、常见错误

| 场景 | 响应 |
|------|------|
| 未登录访问 `bill_monthly` | `{"status":401,"msg":"未登录或登录已过期"}` |
| 轮播图/推荐产品显示状态已达 4 个再新增或开启 | `{"status":400,"msg":"最多可显示4个轮播图/推荐产品，请先隐藏部分后再新增或开启"}` |
| 必填字段缺失（如 `title`/`name`） | `{"status":400,"msg":"标题不能为空/名称不能为空"}` |
| 记录不存在 | `{"status":400,"msg":"轮播图不存在/推荐产品不存在/合作伙伴不存在"}` |
| 无后台权限 | `{"status":404,"msg":"无权访问"}` |

---

## 四、图片地址说明

- 前台/后台使用 `/images/home/banners/*.jpg`、`/images/home/partners/*.png` 形式的站内相对路径。
- 部署时这些图片由 FurLLV10 构建脚本复制到系统 `public/images/` 下，URL 即站点根 `/images/...`。
