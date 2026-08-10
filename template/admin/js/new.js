(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    Vue.prototype.lang = Object.assign(window.lang || {}, window.plugin_lang || {});
    const template = document.getElementsByClassName("furll-home-config")[0];
    const host = location.origin;
    const fir = location.pathname.split("/")[1];
    const str = `${host}/${fir}/`;
    new Vue({
      components: {
        comConfig,
        comPagination
      },
      data() {
        return {
          activeTab: "banner",

          // 通用
          submitLoading: false,
          delVisible: false,
          delType: "",
          delRow: null,
          uploadUrl: str + "v1/upload",
          uploadHeaders: {
            Authorization: "Bearer" + " " + localStorage.getItem("backJwt"),
          },

          // 轮播图
          bannerParams: { keywords: "", page: 1, limit: 20, orderby: "id", sort: "desc" },
          bannerList: [],
          bannerLoading: false,
          bannerVisible: false,
          bannerColumns: [
            { width: 80, colKey: "id", title: "ID" },
            { colKey: "image", title: lang.banner_image, cell: "image", width: 120 },
            { minWidth: 160, colKey: "title", title: lang.banner_title, ellipsis: true },
            { minWidth: 120, colKey: "label", title: lang.banner_label },
            { minWidth: 160, colKey: "description", title: lang.banner_description, ellipsis: true },
            { minWidth: 120, colKey: "url", title: lang.banner_url, ellipsis: true },
            { minWidth: 100, colKey: "button_text", title: lang.banner_button_text },
            { width: 80, colKey: "sort", title: lang.sort },
            { width: 100, colKey: "hidden", title: lang.hidden, cell: "hidden" },
            { colKey: "op", title: lang.operation, cell: "op", fixed: "right", width: 100 },
          ],
          bannerForm: {},
          bannerRules: {
            title: [{ required: true, message: `${lang.input}${lang.banner_title}` }],
          },
          bannerImageFiles: [],

          // 推荐产品
          recommendParams: { keywords: "", page: 1, limit: 20, orderby: "id", sort: "desc" },
          recommendList: [],
          recommendLoading: false,
          recommendVisible: false,
          recommendEnabled: 1,
          recommendColumns: [
            { width: 80, colKey: "id", title: "ID" },
            { minWidth: 160, colKey: "name", title: lang.recommend_name, ellipsis: true },
            { width: 100, colKey: "product_id", title: lang.recommend_product_id },
            { minWidth: 120, colKey: "tag", title: lang.recommend_tag },
            { width: 100, colKey: "price", title: lang.recommend_price },
            { minWidth: 100, colKey: "unit", title: lang.recommend_unit },
            { minWidth: 160, colKey: "description", title: lang.recommend_description, ellipsis: true },
            { width: 80, colKey: "sort", title: lang.sort },
            { width: 100, colKey: "hidden", title: lang.hidden, cell: "hidden" },
            { colKey: "op", title: lang.operation, cell: "op", fixed: "right", width: 100 },
          ],
          recommendForm: {},
          recommendRules: {
            name: [{ required: true, message: `${lang.input}${lang.recommend_name}` }],
          },

          // 合作伙伴
          partnerParams: { keywords: "", page: 1, limit: 20, orderby: "id", sort: "desc" },
          partnerList: [],
          partnerLoading: false,
          partnerVisible: false,
          partnerColumns: [
            { width: 80, colKey: "id", title: "ID" },
            { colKey: "image", title: lang.partner_image, cell: "image", width: 120 },
            { minWidth: 160, colKey: "name", title: lang.partner_name, ellipsis: true },
            { minWidth: 120, colKey: "url", title: lang.partner_url, ellipsis: true },
            { width: 100, colKey: "wall", title: lang.partner_wall, cell: "wall" },
            { width: 80, colKey: "sort", title: lang.sort },
            { width: 100, colKey: "hidden", title: lang.hidden, cell: "hidden" },
            { colKey: "op", title: lang.operation, cell: "op", fixed: "right", width: 100 },
          ],
          partnerForm: {},
          partnerRules: {
            name: [{ required: true, message: `${lang.input}${lang.partner_name}` }],
          },
          partnerImageFiles: [],
        };
      },

      methods: {
        // ---------- 轮播图 ----------
        async getBannerList(page = 1) {
          this.bannerParams.page = page;
          this.bannerLoading = true;
          try {
            const res = await getBannerList(this.bannerParams);
            this.bannerList = res.data.data.list;
          } catch (e) {
            this.$message.error(e.data?.msg || lang.no_data);
          } finally {
            this.bannerLoading = false;
          }
        },
        openBannerDialog(row) {
          this.bannerForm = row ? { ...row } : { title: "", label: "", description: "", image: "", url: "", button_text: "", sort: 0, hidden: 0 };
          this.bannerImageFiles = row && row.image ? [{ url: row.image, name: row.image.split("/").pop(), status: "success" }] : [];
          this.bannerVisible = true;
        },
        closeBannerDialog() {
          this.bannerVisible = false;
        },
        saveBanner() {
          this.$refs.bannerForm.validate().then(async (valid) => {
            if (valid === true) {
              this.submitLoading = true;
              try {
                const res = this.bannerForm.id
                  ? await editBanner({ ...this.bannerForm, id: this.bannerForm.id })
                  : await addBanner(this.bannerForm);
                this.$message.success(res.data.msg);
                this.bannerVisible = false;
                this.getBannerList(this.bannerParams.page);
              } catch (e) {
                this.$message.error(e.data?.msg || lang.fail_message);
              } finally {
                this.submitLoading = false;
              }
            }
          });
        },
        onBannerUploadSuccess(file) {
          if (file.response && file.response.url) {
            this.bannerForm.image = file.response.url;
          }
        },
        onBannerRemove() {
          this.bannerForm.image = "";
        },

        // ---------- 推荐产品 ----------
        async getRecommendList(page = 1) {
          this.recommendParams.page = page;
          this.recommendLoading = true;
          try {
            const res = await getRecommendList(this.recommendParams);
            this.recommendList = res.data.data.list;
          } catch (e) {
            this.$message.error(e.data?.msg || lang.no_data);
          } finally {
            this.recommendLoading = false;
          }
        },
        openRecommendDialog(row) {
          this.recommendForm = row ? { ...row } : { product_id: 0, name: "", description: "", tag: "", price: "", unit: "", url: "", sort: 0, hidden: 0 };
          this.recommendVisible = true;
        },
        closeRecommendDialog() {
          this.recommendVisible = false;
        },
        saveRecommend() {
          this.$refs.recommendForm.validate().then(async (valid) => {
            if (valid === true) {
              this.submitLoading = true;
              try {
                const res = this.recommendForm.id
                  ? await editRecommend({ ...this.recommendForm, id: this.recommendForm.id })
                  : await addRecommend(this.recommendForm);
                this.$message.success(res.data.msg);
                this.recommendVisible = false;
                this.getRecommendList(this.recommendParams.page);
              } catch (e) {
                this.$message.error(e.data?.msg || lang.fail_message);
              } finally {
                this.submitLoading = false;
              }
            }
          });
        },

        // ---------- 合作伙伴 ----------
        async getPartnerList(page = 1) {
          this.partnerParams.page = page;
          this.partnerLoading = true;
          try {
            const res = await getPartnerList(this.partnerParams);
            this.partnerList = res.data.data.list;
          } catch (e) {
            this.$message.error(e.data?.msg || lang.no_data);
          } finally {
            this.partnerLoading = false;
          }
        },
        openPartnerDialog(row) {
          this.partnerForm = row ? { ...row } : { name: "", image: "", url: "", wall: 1, sort: 0, hidden: 0 };
          this.partnerImageFiles = row && row.image ? [{ url: row.image, name: row.image.split("/").pop(), status: "success" }] : [];
          this.partnerVisible = true;
        },
        closePartnerDialog() {
          this.partnerVisible = false;
        },
        savePartner() {
          this.$refs.partnerForm.validate().then(async (valid) => {
            if (valid === true) {
              this.submitLoading = true;
              try {
                const res = this.partnerForm.id
                  ? await editPartner({ ...this.partnerForm, id: this.partnerForm.id })
                  : await addPartner(this.partnerForm);
                this.$message.success(res.data.msg);
                this.partnerVisible = false;
                this.getPartnerList(this.partnerParams.page);
              } catch (e) {
                this.$message.error(e.data?.msg || lang.fail_message);
              } finally {
                this.submitLoading = false;
              }
            }
          });
        },
        onPartnerUploadSuccess(file) {
          if (file.response && file.response.url) {
            this.partnerForm.image = file.response.url;
          }
        },
        onPartnerRemove() {
          this.partnerForm.image = "";
        },

        // ---------- 通用 ----------
        async onSwitch(type, row) {
          // 受控 t-switch 回传的 val 恒为当前值，直接读 row.hidden 取相反状态
          const hidden = Number(row.hidden) === 0 ? 1 : 0;
          try {
            if (type === "banner") {
              await editBanner({ ...row, id: row.id, hidden });
            } else if (type === "recommend") {
              await editRecommend({ ...row, id: row.id, hidden });
            } else if (type === "partner") {
              await editPartner({ ...row, id: row.id, hidden });
            }
            // 以服务端为准重新拉取，保证开关状态与接口返回一致
            if (type === "banner") {
              this.getBannerList(this.bannerParams.page);
            } else if (type === "recommend") {
              this.getRecommendList(this.recommendParams.page);
            } else {
              this.getPartnerList(this.partnerParams.page);
            }
            this.$message.success(lang.update_success);
          } catch (e) {
            this.$message.error(e.data?.msg || lang.fail_message);
          }
        },
        deleteRow(type, row) {
          this.delType = type;
          this.delRow = row;
          this.delVisible = true;
        },
        async sureDelete() {
          this.submitLoading = true;
          try {
            let res;
            if (this.delType === "banner") {
              res = await deleteBanner({ id: this.delRow.id });
            } else if (this.delType === "recommend") {
              res = await deleteRecommend({ id: this.delRow.id });
            } else if (this.delType === "partner") {
              res = await deletePartner({ id: this.delRow.id });
            }
            this.$message.success(res.data.msg);
            this.delVisible = false;
            if (this.delType === "banner") this.getBannerList(this.bannerParams.page);
            if (this.delType === "recommend") this.getRecommendList(this.recommendParams.page);
            if (this.delType === "partner") this.getPartnerList(this.partnerParams.page);
          } catch (e) {
            this.$message.error(e.data?.msg || lang.fail_message);
          } finally {
            this.submitLoading = false;
          }
        },

        async getConfig() {
          try {
            const res = await getFurllHomeConfig();
            const config = res.data.data.config || {};
            this.recommendEnabled = parseInt(config.recommend_enabled ?? "1");
          } catch (e) {
            this.recommendEnabled = 1;
          }
        },
        async saveConfig(val) {
          this.recommendEnabled = val;
          try {
            await updateFurllHomeConfig({ recommend_enabled: val });
            this.$message.success(lang.update_success);
          } catch (e) {
            this.$message.error(e.data?.msg || lang.fail_message);
          }
        },

        // 上传
        beforeUpload(file) {
          return true;
        },
        formatResponse(res) {
          if (res.status != 200) {
            return { error: res.msg };
          }
          return {
            name: res.data.save_name,
            url: res.data.image_url,
          };
        },
        handleFail() {
          this.$message.error(lang.upload_image + lang.fail_message);
        },
      },

      mounted() {
        this.getBannerList(1);
        this.getRecommendList(1);
        this.getPartnerList(1);
        this.getConfig();
      },
    }).$mount(template);
  };
})(window, undefined);
