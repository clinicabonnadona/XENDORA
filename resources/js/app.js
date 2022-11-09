require("./bootstrap");

/* Imported Packages */
import Vue from "vue";
import Vuelidate from "vuelidate";
import PortalVue from "portal-vue";
//import 'bootstrap/dist/css/bootstrap.css'
import "bootstrap-vue/dist/bootstrap-vue.css";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import "vue-select/dist/vue-select.css";
import vSelect from "vue-select";
import { ClientTable } from "vue-tables-2";
import Vue2Filters from "vue2-filters";
import VueCurrencyInput from "vue-currency-input";
import Popover from "vue-js-popover";
import moment from "moment";
import excel from "vue-excel-export";

/* Vuex Store */
import store from "./store";

/* Imported Local Files */

Vue.config.productionTip = false;
const pluginOptions = {
    /* see config reference */
    globalOptions: { currency: { prefix: "$" }, locale: "es", precision: 0 }
};

/* Using Packages */
Vue.use(Vuelidate);
Vue.use(PortalVue);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.component("v-select", vSelect);
Vue.use(ClientTable, {}, false, "bootstrap4");
Vue.use(Vue2Filters);
Vue.use(VueCurrencyInput, pluginOptions);
Vue.use(Popover);
Vue.use(excel);

String.prototype.trunc = function(n) {
    return this.substr(0, n - 1) + (this.length > n ? "..." : "");
};

Vue.prototype.moment = moment;

//Directives
Vue.directive("can", function(el, binding, vnode) {
    if (Permissions.indexOf(binding.value) !== -1) {
        return (vnode.elm.hidden = false);
    } else {
        return (vnode.elm.hidden = true);
    }
});

axios.defaults.baseURL = "http://127.0.0.1:8000/api/v1/";
//axios.defaults.baseURL = 'http://172.16.52.162:8086/api/v1/'
//axios.defaults.baseURL = 'http://190.131.222.108:8085/api/v1/'

/* Vue Components */
Vue.component(
    "users-component",
    require("./components/admin/user/Users").default
);

Vue.component(
    "roles-component",
    require("./components/admin/roles/Roles").default
);
Vue.component(
    "permissions-component",
    require("./components/admin/permissions/Permissions").default
);
Vue.component(
    "suministros-component",
    require("./components/admin/suministros/Suministros").default
);
Vue.component(
    "terceros-component",
    require("./components/admin/terceros/Terceros").default
);
Vue.component(
    "censo-component",
    require("./components/shared/reportes/CensoDiario").default
);
Vue.component(
    "rotacion-general-component",
    require("./components/shared/rotaciones/Rotacion").default
);
Vue.component(
    "nomina1-component",
    require("./components/shared/reportes/Nomina/Nomina1").default
);
Vue.component(
    "nomina2-component",
    require("./components/shared/reportes/Nomina/Nomina2").default
);
Vue.component(
    "nomina3-component",
    require("./components/shared/reportes/Nomina/Nomina3").default
);
Vue.component(
    "rrhh1-component",
    require("./components/shared/reportes/RRHH/Rrhh1").default
);
Vue.component(
    "rrhh2-component",
    require("./components/shared/reportes/RRHH/Rrhh2").default
);
Vue.component(
    "report-prealta-component",
    require("./components/shared/reportes/Prealta/Prealta").default
);
Vue.component(
    "report-auditoria-med-amb",
    require("./components/shared/reportes/Auditoria/MedicamentosAmbulatorios")
        .default
);
Vue.component(
    "report-marcaciones",
    require("./components/shared/reportes/Egresos/MarcacionesHechas").default
);
Vue.component(
    "farmacia-recepcion-component",
    require("./components/shared/farmacia/RecepcionMedicamentos").default
);
Vue.component(
    "farmacia-envio-ordenes-component",
    require("./components/shared/farmacia/ShippingPurchaseOrders").default
);
Vue.component(
    "glucometrias-reporte-component",
    require("./components/shared/reportes/Glucometria/GlucometriasReporte")
        .default
);
Vue.component(
    "devolucion-reporte-component",
    require("./components/shared/reportes/Devoluciones/DevolucionReporte")
        .default
);
Vue.component(
    "devolucion-total-reporte-component",
    require("./components/shared/reportes/Devoluciones/DevolucionesTotal")
        .default
);

Vue.component(
    "familiy-income-records",
    require("./components/shared/userOrientation/UserOrientation.vue").default
);

Vue.component(
    "seg-evoluciones-fact",
    require("./components/shared/reportes/Auditoria/SeguimientoEvolucionesFact.vue")
        .default
);

Vue.component(
    "compras-seguimiento",
    require("./components/shared/compras/ComprasSeguimiento.vue").default
);

// =======================================
// Providers Evaluations Main Component
Vue.component(
    "provider-evaluation-component",
    require("./components/shared/farmacia/ProviderEvaluation.vue").default
);
// =======================================

// =======================================
// Site in maintenance
Vue.component(
    "site-in-maintenance",
    require("./components/shared/maintenance/Maintenance.vue").default
);
// =======================================

const app = new Vue({
    el: "#app",
    store
});
