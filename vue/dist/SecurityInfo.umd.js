(function(global, factory) {
  typeof exports === "object" && typeof module !== "undefined" ? factory(exports, require("vue"), require("CoreHome")) : typeof define === "function" && define.amd ? define(["exports", "vue", "CoreHome"], factory) : (global = typeof globalThis !== "undefined" ? globalThis : global || self, factory(global.SecurityInfo = {}, global.Vue, global.CoreHome));
})(this, (function(exports2, vue, CoreHome) {
  "use strict";
  const _sfc_main = vue.defineComponent({
    props: {
      results: {
        type: Object,
        required: true
      }
    },
    components: {
      ContentBlock: CoreHome.ContentBlock
    },
    directives: {
      ContentIntro: CoreHome.ContentIntro,
      ContentTable: CoreHome.ContentTable
    }
  });
  const _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _hoisted_1 = ["href"];
  const _hoisted_2 = { class: "alert-info alert" };
  const _hoisted_3 = ["href"];
  const _hoisted_4 = { class: "securityInfoTable" };
  const _hoisted_5 = { class: "test" };
  const _hoisted_6 = ["innerHTML"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ContentBlock = vue.resolveComponent("ContentBlock");
    const _directive_content_intro = vue.resolveDirective("content-intro");
    const _directive_content_table = vue.resolveDirective("content-table");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.withDirectives((vue.openBlock(), vue.createElementBlock("div", null, [
        vue.createElementVNode("h2", null, vue.toDisplayString(_ctx.translate("SecurityInfo_SecurityInformation")), 1),
        vue.createElementVNode("p", null, vue.toDisplayString(_ctx.translate("SecurityInfo_PluginDescription")), 1),
        vue.createElementVNode("p", null, [
          _cache[0] || (_cache[0] = vue.createTextVNode(" Learn more: read our guide ", -1)),
          vue.createElementVNode("a", {
            rel: "noreferrer",
            target: "_blank",
            href: _ctx.externalRawLink("https://matomo.org/faq/on-premise/how-to-configure-matomo-for-security/")
          }, " Hardening Matomo: How to make Matomo and your web server more secure? ", 8, _hoisted_1)
        ]),
        vue.createElementVNode("p", _hoisted_2, [
          _cache[1] || (_cache[1] = vue.createTextVNode(" Did you know? The ", -1)),
          vue.createElementVNode("a", {
            target: "_blank",
            href: _ctx.externalRawLink("https://plugins.matomo.org/ActivityLog"),
            rel: "noreferrer noopener"
          }, "Activity Log plugin for Matomo", 8, _hoisted_3),
          _cache[2] || (_cache[2] = vue.createTextVNode(" improves your security and diagnostic by letting you quickly review the actions performed by members of your organization or clients in your Matomo. ", -1))
        ])
      ])), [
        [_directive_content_intro]
      ]),
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(_ctx.results.test_results, (section, i) => {
        return vue.openBlock(), vue.createBlock(_component_ContentBlock, {
          key: i,
          "content-title": i
        }, {
          default: vue.withCtx(() => [
            vue.withDirectives((vue.openBlock(), vue.createElementBlock("table", _hoisted_4, [
              vue.createElementVNode("thead", null, [
                vue.createElementVNode("tr", null, [
                  vue.createElementVNode("th", null, vue.toDisplayString(_ctx.translate("SecurityInfo_Test")), 1),
                  vue.createElementVNode("th", null, vue.toDisplayString(_ctx.translate("SecurityInfo_Result")), 1)
                ])
              ]),
              vue.createElementVNode("tbody", null, [
                (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(section, (test, j) => {
                  return vue.openBlock(), vue.createElementBlock("tr", { key: j }, [
                    vue.createElementVNode("td", _hoisted_5, vue.toDisplayString(j), 1),
                    vue.createElementVNode("td", {
                      class: vue.normalizeClass([-1, -2, -4].indexOf(test.result) !== -1 ? `testresult${-test.result}` : "testresult5"),
                      innerHTML: _ctx.$sanitize(test.message)
                    }, null, 10, _hoisted_6)
                  ]);
                }), 128))
              ])
            ])), [
              [_directive_content_table]
            ])
          ]),
          _: 2
        }, 1032, ["content-title"]);
      }), 128))
    ]);
  }
  const TestResultsPage = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  exports2.TestResultsPage = TestResultsPage;
  Object.defineProperty(exports2, Symbol.toStringTag, { value: "Module" });
}));
