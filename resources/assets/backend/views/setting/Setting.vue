<template>
  <div class="setting">
    <Panel :title="title">
      <Form ref="form" :rules="rules" :model="formData" :label-width="100">
        <Form-item label="设置项" prop="name" :error="errors.name">
          <Input v-model="formData.name" placeholder="请填写设置项"></Input>
        </Form-item>
        <Form-item label="分类" :error="errors.type_name" prop="type_name">
          <Select v-model="formData.type_name" style="width:200px">
            <Option v-for="item in types" :value="item.name" :key="item.id">{{ item.display_name }}</Option>
          </Select>
        </Form-item>
        <Form-item label="设置值" prop="value" :error="errors.value">
          <Input v-model="formData.value" type="textarea" :rows="4" placeholder="请填写设置值"></Input>
        </Form-item>
        <Form-item label="描述" prop="description" :error="errors.description">
          <Input v-model="formData.description" type="textarea" :rows="4" placeholder="请输入描述"></Input>
        </Form-item>
        <FormButtomGroup />
      </Form>
    </Panel>
  </div>
</template>

<script>
import Panel from '../../components/Panel.vue';
import fromMixin from '../../mixins/form';
import FormButtomGroup from '../../components/FormButtonGroup.vue';
export default {
  components: { Panel, FormButtomGroup },
  mixins: [ fromMixin ],
  computed: {
    rules () {
      return {
        name: [
          { required: true, type: 'string', message: '请填写设置项', trigger: 'blur' }
        ],
        value: [
          { required: true, type: 'string', message: '请填写设置值', trigger: 'blur' }
        ],
        type_name: [
          { required: true, type: 'string', message: '请选择分类', trigger: 'change' }
        ]
      };
    },
    mixinConfig () {
      return {
        title: '站点设置',
        action: this.isAdd() ? 'settings' : `settings/${this.$route.params.id}`,
      };
    }
  },
  mounted () {
    this.$on('on-success', () => {
      this.$router.push({name: 'settingList'});
    });
    this.$http.get('types', {
      params: {
        model: 'settings'
      }
    }).then(res => {
      this.types = res.data.data;
    });
  },
  data () {
    return {
      formData: {
        'name': null,
        'value': null,
        'description': null,
        'type_name': null
      },
      types: []
    };
  }
};
</script>