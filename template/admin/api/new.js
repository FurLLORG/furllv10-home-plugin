// 轮播图
function getBannerList(params) {
  return Axios.get("/furll_home/banner", { params: {...params} });
}
function getBannerDetail(params) {
  return Axios.get(`/furll_home/banner/${params.id}`);
}
function addBanner(params) {
  return Axios.post("/furll_home/banner", params);
}
function editBanner(params) {
  return Axios.put(`/furll_home/banner/${params.id}`, params);
}
function deleteBanner(params) {
  return Axios.delete(`/furll_home/banner/${params.id}`);
}

// 推荐产品
function getRecommendList(params) {
  return Axios.get("/furll_home/recommend", { params: {...params} });
}
function getRecommendDetail(params) {
  return Axios.get(`/furll_home/recommend/${params.id}`);
}
function addRecommend(params) {
  return Axios.post("/furll_home/recommend", params);
}
function editRecommend(params) {
  return Axios.put(`/furll_home/recommend/${params.id}`, params);
}
function deleteRecommend(params) {
  return Axios.delete(`/furll_home/recommend/${params.id}`);
}

// 合作伙伴
function getPartnerList(params) {
  return Axios.get("/furll_home/partner", { params: {...params} });
}
function getPartnerDetail(params) {
  return Axios.get(`/furll_home/partner/${params.id}`);
}
function addPartner(params) {
  return Axios.post("/furll_home/partner", params);
}
function editPartner(params) {
  return Axios.put(`/furll_home/partner/${params.id}`, params);
}
function deletePartner(params) {
  return Axios.delete(`/furll_home/partner/${params.id}`);
}

// 配置
function getFurllHomeConfig() {
  return Axios.get("/furll_home/config");
}
function updateFurllHomeConfig(params) {
  return Axios.put("/furll_home/config", params);
}
