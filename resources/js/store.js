import Vue from "vue";
import Vuex from "vuex";

// Modules
import Farmacia from "./store/modules/shared/FarmaciaRecepcion";
import Rotacion from "./store/modules/shared/Rotacion";
import RGlucometry from "./store/modules/shared/GlucometriasReporte";
import RDevolucion from "./store/modules/shared/DevolucionReporte";
import RDevolucionTotal from "./store/modules/shared/DevolucionTotalReporte";
import Permissions from "./store/modules/admin/Permissions.store";
import Roles from "./store/modules/admin/Roles.store";
import Users from "./store/modules/admin/Users.store";
import ShippingPurchaseOrders from "./store/modules/shared/ShippingPurchaseOrders";
import SeguimientoDeEvoluciones from "./store/modules/shared/SeguimientoDevoluciones";
import FamilyIncomeRecord from "./store/modules/shared/FamilyIncomeRecord";
import ComprasSeguimientos from "./store/modules/shared/ComprasSeguimiento";
import ProviderEvaliations from "./store/modules/shared/ProviderEvaluations";
import ProveedoresCausacion from "./store/modules/shared/ProveedoresCausacion";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        f: Farmacia,
        r: Rotacion,
        rg: RGlucometry,
        rd: RDevolucion,
        rdt: RDevolucionTotal,
        p: Permissions,
        ro: Roles,
        u: Users,
        so: ShippingPurchaseOrders,
        sde: SeguimientoDeEvoluciones,
        uor: FamilyIncomeRecord,
        cs: ComprasSeguimientos,
        proe: ProviderEvaliations,
        pc: ProveedoresCausacion
    }
});
