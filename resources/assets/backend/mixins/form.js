import diff from '../utils/diff';
export default {
  data () {
    return {
      id: null,
      title: '',
      errors: []
    };
  },
  methods: {
    confirm () {
      let method, url;
      if (this.id) {
        method = 'put';
        url = `${this.$options.base.url}/${this.id}`;
      } else {
        method = 'post';
        url = this.$options.base.url;
      }
      this.$http[method](url, diff.diff(this.formData)).then(res => {
        this.$Message.success(`${this.title}成功`);
        if (typeof this.onSuccess === 'function') {
          this.onSuccess();
        }
      }).catch(err => {
        this.errors = err.response.data.errors;
      });
    }
  },
  created () {
    if (this.$route.name.substring(0, 4) === 'edit') {
      this.id = this.$route.params.id;
      this.$http.get(`${this.$options.base.url}/${this.id}`).then(res => {
        this.formData = res.data.data;
        diff.save(this.formData);
        if (typeof this.onData === 'function') {
          this.onData(res.data);
        }
      });
      this.title = `编辑${this.$options.base.title}`;
    } else {
      this.title = `添加${this.$options.base.title}`;
    }
  }
};