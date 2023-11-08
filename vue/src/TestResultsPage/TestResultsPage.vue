<!--
  Matomo - free/libre analytics platform

  @link https://matomo.org
  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <div>
    <div v-content-intro>
      <h2>{{ translate('SecurityInfo_SecurityInformation') }}</h2>
      <p>{{ translate('SecurityInfo_PluginDescription') }}</p>
      <p>
        Learn more: read our guide
        <a rel='noreferrer' target='_blank' :href='externalRawLink("https://matomo.org/docs/security/")'>
          Hardening Matomo: How to make Matomo and your web server more secure?
        </a>
      </p>

      <p class="alert-info alert">
        Did you know? The
        <a
          target='_blank'
          :href='externalRawLink("https://plugins.matomo.org/ActivityLog")'
          rel="noreferrer noopener"
        >Activity Log plugin for Matomo</a>
        improves your security and diagnostic by letting you quickly review the actions
        performed by members of your organization or clients in your Matomo.
      </p>
    </div>
    <ContentBlock
      v-for="(section, i) in results.test_results"
      :key="i"
      :content-title="i"
    >
      <table v-content-table class="securityInfoTable">
        <thead>
        <tr>
          <th>{{ translate('SecurityInfo_Test') }}</th>
          <th>{{ translate('SecurityInfo_Result') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(test, j) in section" :key="j">
          <td class="test">{{ j }}</td>
          <td
            :class="[-1, -2, -4].indexOf(test.result) !== -1
              ? `testresult${-test.result}` : 'testresult5'"
            v-html="$sanitize(test.message)"
          ></td>
        </tr>
        </tbody>
      </table>
    </ContentBlock>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  ContentIntro,
  ContentTable,
  ContentBlock,
} from 'CoreHome';

export default defineComponent({
  props: {
    results: {
      type: Object,
      required: true,
    },
  },
  components: {
    ContentBlock,
  },
  directives: {
    ContentIntro,
    ContentTable,
  },
});
</script>
