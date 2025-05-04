// 現在のbootstrap.jsの内容をTypescriptに変換
import axios from 'axios';

declare global {
  interface Window {
    axios: typeof axios;
  }
}

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';