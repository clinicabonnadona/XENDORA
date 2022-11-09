<template>
    <div class="main-content-permission">
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-container fluid>
            <b-row>
                <b-col cols="12">
                    <b-card
                        header-tag="header"
                        no-body
                        border-variant="secondary"
                        header-border-variant="secondary"
                        class="card-content text-right"
                    >
                        <template #header>
                            <b-row>
                                <b-col>
                                    <b-form-group class="mb-0">
                                        <b-input-group>
                                            <b-form-input
                                                v-model="filter"
                                                type="search"
                                                id="filterInput"
                                                placeholder="Escribe Aquí para Buscar"
                                                class="login-search-input"
                                            ></b-form-input>
                                            <b-input-group-append>
                                                <b-button
                                                    :disabled="!filter"
                                                    @click="filter = ''"
                                                    class="login-search-input-append"
                                                >
                                                    <i
                                                        class="fas fa-paint-brush"
                                                    ></i>
                                                </b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </b-form-group>
                                </b-col>

                                <b-col class="float-right">
                                    <b-button
                                        variant="warning"
                                        size="sm"
                                        pill
                                        title="Recargar"
                                        @click="refreshData"
                                        class="button-times-icon"
                                    >
                                        <i
                                            class="fas fa-sync-alt times-icon"
                                        ></i>
                                    </b-button>

                                    <!-- BUTTON CREATE USERS -->
                                    <b-button
                                        variant="primary"
                                        size="sm"
                                        pill
                                        title="Crear Nuevo Permiso"
                                        @click="showModal('modal-new-user')"
                                        class="button-times-icon"
                                    >
                                        <i class="fas fa-plus times-icon"></i>
                                        Crear Usuario
                                    </b-button>
                                </b-col>
                            </b-row>
                        </template>

                        <b-card-body>
                            <b-row>
                                <b-col xl="12" class="my-1">
                                    <b-table
                                        :items="getActiveUsers"
                                        :fields="fields"
                                        :current-page="currentPage"
                                        :per-page="perPage"
                                        :select-mode="selectMode"
                                        :filter="filter"
                                        :filter-included-fields="filterOn"
                                        responsive
                                        hover
                                        bordered
                                        ref="selectableTable"
                                        selectable
                                        head-variant="dark"
                                        small
                                        show-empty
                                        :busy="activeUsersIsLoading"
                                        @filtered="onFiltered"
                                        @row-selected="onRowSelected"
                                    >
                                        <template #table-busy>
                                            <div
                                                class="text-center text-danger my-2"
                                            >
                                                <b-spinner
                                                    class="align-middle"
                                                ></b-spinner>
                                                <strong>Loading...</strong>
                                            </div>
                                        </template>

                                        <template #cell(roles)="row">
                                            <b-badge
                                                pill
                                                size="sm"
                                                variant="primary"
                                                v-for="(item,
                                                index) in row.value"
                                                :key="index"
                                            >
                                                {{ item.name }}
                                            </b-badge>
                                        </template>

                                        <template #cell(state)="row">
                                            <i
                                                class="fas fa-user-circle fa-2x"
                                                :style="
                                                    row.value == 0
                                                        ? 'color: red'
                                                        : 'color: green'
                                                "
                                            ></i>
                                        </template>

                                        <template #cell(actions)="row">
                                            <b-button
                                                pill
                                                size="sm"
                                                variant="danger"
                                                title="Desactivar Permiso"
                                                @click="desactivateUser(row)"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </b-button>
                                        </template>
                                    </b-table>
                                </b-col>
                            </b-row>

                            <b-row>
                                <b-col xl="4" offset-xl="4" class="my-1">
                                    <b-pagination
                                        v-model="currentPage"
                                        :total-rows="getActiveUsersCount"
                                        :per-page="perPage"
                                        align="fill"
                                        size="sm"
                                        class="my-0"
                                    ></b-pagination>
                                </b-col>
                            </b-row>
                        </b-card-body>

                        <template #footer>
                            <b-row>
                                <b-col cols="12" class="text-center">
                                    total de registros:
                                    {{ getActiveUsersCount }}
                                </b-col>
                            </b-row>
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>

        <!-- MODALS FROM THIS COMPONENT -->
        <!-- CREATE USER -->
        <b-modal
            ref="modal-new-user"
            no-close-on-backdrop
            no-close-on-esc
            size="lg"
        >
            <template #modal-header>
                <p class="m-0">CREAR USUARIO</p>
            </template>

            <template #default>
                <b-container fluid class="mt-2">
                    <b-row>
                        <b-col cols="12">
                            <b-form
                                autocomplete="off"
                                @submit.prevent="saveUser()"
                            >
                                <b-row>
                                    <b-col cols="6">
                                        <b-form-group
                                            label="Nombre:"
                                            id="input-group-1"
                                        >
                                            <b-input
                                                placeholder="Nombre"
                                                id="input-1"
                                                v-model.trim="user.name"
                                                aria-describedby="input-1-live-feedback"
                                            ></b-input>

                                            <span
                                                class="text-danger"
                                                v-if="getErrorUsers"
                                            >
                                                {{ getErrorUsers.name }}
                                            </span>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-group
                                            label="Apellido:"
                                            id="input-group-2"
                                        >
                                            <b-input
                                                placeholder="Apellido"
                                                id="input-2"
                                                v-model.trim="user.lastName"
                                                aria-describedby="input-2-live-feedback"
                                            ></b-input>

                                            <span
                                                class="text-danger"
                                                v-if="getErrorUsers"
                                            >
                                                {{ getErrorUsers.lastName }}
                                            </span>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col cols="12">
                                        <b-form-group
                                            label="Usuario:"
                                            id="input-group-3"
                                        >
                                            <b-input
                                                placeholder="Usuario"
                                                id="input-3"
                                                v-model="user.username"
                                                aria-describedby="input-3-live-feedback"
                                            ></b-input>

                                            <span
                                                class="text-danger"
                                                v-if="getErrorUsers"
                                            >
                                                {{ getErrorUsers.username }}
                                            </span>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col cols="6">
                                        <b-form-group
                                            label="Tipo de documento:"
                                            id="input-group-4"
                                        >
                                            <b-form-select
                                                id="select-doctype"
                                                v-model="user.tDocument"
                                                aria-describedby="input-4-live-feedback"
                                            >
                                                <b-form-select-option
                                                    :value="null"
                                                >
                                                    Selecciona el tipo de
                                                    documento
                                                </b-form-select-option>
                                                <b-form-select-option
                                                    v-for="(item,
                                                    index) in getActiveDoctypes"
                                                    :key="index"
                                                    :value="item.id"
                                                >
                                                    {{ item.name }} -
                                                    {{ item.description }}
                                                </b-form-select-option>
                                            </b-form-select>

                                            <span
                                                class="text-danger"
                                                v-if="getErrorUsers"
                                            >
                                                {{ getErrorUsers.tDocument }}
                                            </span>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-group
                                            label="Número de documento:"
                                            id="input-group-5"
                                        >
                                            <b-input
                                                placeholder="Número de documento"
                                                id="input-5"
                                                v-model="user.document"
                                                aria-describedby="input-5-live-feedback"
                                            ></b-input>

                                            <span
                                                class="text-danger"
                                                v-if="getErrorUsers"
                                            >
                                                {{ getErrorUsers.document }}
                                            </span>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col cols="6">
                                        <b-form-group
                                            label="Dirección:"
                                            id="input-group-6"
                                        >
                                            <b-input
                                                placeholder="Dirección"
                                                id="input-6"
                                                v-model="user.address"
                                            ></b-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-group
                                            label="Número de Telefono:"
                                            id="input-group-7"
                                        >
                                            <b-input
                                                placeholder="Número de Telefono"
                                                id="input-7"
                                                v-model="user.phone"
                                            ></b-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col cols="12">
                                        <b-form-group
                                            label="Email:"
                                            id="input-group-8"
                                        >
                                            <b-input
                                                placeholder="Email"
                                                id="input-8"
                                                type="email"
                                                v-model="user.email"
                                                aria-describedby="input-8-live-feedback"
                                            ></b-input>

                                            <span
                                                class="text-danger"
                                                v-if="getErrorUsers"
                                            >
                                                {{ getErrorUsers.email }}
                                            </span>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col cols="12">
                                        <b-form-group
                                            label="Contraseña:"
                                            id="input-group-9"
                                        >
                                            <b-input
                                                placeholder="Contraseña"
                                                id="input-9"
                                                type="password"
                                                v-model="user.password"
                                                aria-describedby="input-9-live-feedback"
                                            ></b-input>

                                            <span
                                                class="text-danger"
                                                v-if="getErrorUsers"
                                            >
                                                {{ getErrorUsers.password }}
                                            </span>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </b-form>
                        </b-col>
                    </b-row>
                </b-container>
            </template>

            <template #modal-footer>
                <b-button
                    pill
                    variant="warning"
                    @click="clearFields"
                    class="button-times-icon"
                >
                    <i class="fas fa-brush times-icon"></i> Limpiar
                </b-button>
                <b-button
                    pill
                    variant="danger"
                    @click="hideModal('modal-new-user')"
                    class="button-times-icon"
                >
                    <i class="fas fa-times times-icon"></i> Cancelar
                </b-button>
                <b-button
                    pill
                    variant="success"
                    @click="saveUser()"
                    class="button-times-icon"
                >
                    <i class="fas fa-save times-icon"></i> Guardar
                </b-button>
            </template>
        </b-modal>

        <!-- EDIT USER -->
        <b-modal ref="modal-edit-user" no-close-on-backdrop no-close-on-esc>
            <template #modal-header>
                <p class="m-0">EDITAR USUARIO</p>
            </template>

            <template #default>
                <b-container fluid class="mt-3">
                    <b-row>
                        <b-col cols="12">
                            <b-form autocomplete="off">
                                <b-form-group
                                    id="input-group-200"
                                    label="Nombre del Usuario:"
                                    label-for="input-200"
                                >
                                    <b-form-input
                                        id="input-200"
                                        v-model="user.name"
                                        type="text"
                                        placeholder="Edite el nombre del usuario"
                                        required
                                    ></b-form-input>

                                    <span
                                        class="text-danger"
                                        v-if="getErrorUsers"
                                    >
                                        {{ getErrorUsers.name }}
                                    </span>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-201"
                                    label="Apellido del Usuario:"
                                    label-for="input-201"
                                >
                                    <b-form-input
                                        id="input-201"
                                        v-model="user.lastName"
                                        type="text"
                                        placeholder="Edite el apellido del usuario"
                                        required
                                    ></b-form-input>

                                    <span
                                        class="text-danger"
                                        v-if="getErrorUsers"
                                    >
                                        {{ getErrorUsers.lastName }}
                                    </span>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-202"
                                    label="Usuario:"
                                    label-for="input-202"
                                >
                                    <b-form-input
                                        id="input-202"
                                        v-model="user.username"
                                        type="text"
                                        placeholder="Edite el usuario"
                                        required
                                    ></b-form-input>

                                    <span
                                        class="text-danger"
                                        v-if="getErrorUsers"
                                    >
                                        {{ getErrorUsers.username }}
                                    </span>
                                </b-form-group>

                                <b-form-group
                                    label="Tipo de documento:"
                                    id="input-group-205"
                                >
                                    <b-form-select
                                        id="select-doctype"
                                        v-model="user.tDocument"
                                        aria-describedby="input-4-live-feedback"
                                    >
                                        <b-form-select-option :value="null">
                                            Selecciona el tipo de documento
                                        </b-form-select-option>
                                        <b-form-select-option
                                            v-for="(item,
                                            index) in getActiveDoctypes"
                                            :key="index"
                                            :value="item.id"
                                        >
                                            {{ item.name }} -
                                            {{ item.description }}
                                        </b-form-select-option>
                                    </b-form-select>

                                    <span
                                        class="text-danger"
                                        v-if="getErrorUsers"
                                    >
                                        {{ getErrorUsers.tDocument }}
                                    </span>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-203"
                                    label="Documento:"
                                    label-for="input-203"
                                >
                                    <b-form-input
                                        id="input-203"
                                        v-model="user.document"
                                        type="text"
                                        placeholder="Edite el Número de Documento"
                                        required
                                    ></b-form-input>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-204"
                                    label="Email:"
                                    label-for="input-204"
                                >
                                    <b-form-input
                                        id="input-204"
                                        v-model="user.email"
                                        type="text"
                                        placeholder="Edite el Email del Usuario"
                                        required
                                    ></b-form-input>

                                    <span
                                        class="text-danger"
                                        v-if="getErrorUsers"
                                    >
                                        {{ getErrorUsers.email }}
                                    </span>
                                </b-form-group>

                                <b-form-group>
                                    <label for="role-select-input">Rol:</label>
                                    <v-select
                                        multiple
                                        v-model="user.roles"
                                        :reduce="roles => roles.id"
                                        :options="getActiveRoles"
                                        label="name"
                                        placeholder="Seleccionar Rol"
                                        id="role-select-input"
                                    />

                                    <span
                                        class="text-danger"
                                        v-if="getErrorUsers"
                                    >
                                        {{ getErrorUsers.roles }}
                                    </span>
                                </b-form-group>
                            </b-form>
                        </b-col>
                    </b-row>
                </b-container>
            </template>

            <template #modal-footer>
                <b-button
                    pill
                    variant="danger"
                    @click="hideModal('modal-edit-user')"
                    class="button-times-icon"
                >
                    <i class="fas fa-times times-icon"></i> Cancelar
                </b-button>
                <b-button
                    pill
                    variant="warning"
                    @click="editUser()"
                    class="button-times-icon"
                >
                    <i class="fas fa-pen times-icon"></i> Editar
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import NoConnectionAlert from "../../shared/alerts/NoConnectionAlert";
import { required, minLength, email, numeric } from "vuelidate/lib/validators";

export default {
    name: "Users",
    components: {
        NoConnectionAlert
    },
    data() {
        return {
            onLine: navigator.onLine,
            selectMode: "single",
            selected: [],
            totalRow: 1,
            currentPage: 1,
            perPage: 10,
            filter: null,
            filterOn: [],
            fields: [
                { key: "id", label: "#", class: "text-center" },
                { key: "name", label: "Nombre", class: "text-center" },
                { key: "lastName", label: "Apellido", class: "text-center" },
                { key: "username", label: "Usuario", class: "text-center" },
                { key: "doctype.name", label: "T. Doc", class: "text-center" },
                { key: "document", label: "Documento", class: "text-center" },
                { key: "email", label: "Email", class: "text-center" },
                { key: "roles", label: "Rol", class: "text-center" },
                { key: "state", label: "Estado", class: "text-center" },
                { key: "actions", label: "Acciones", class: "text-center" }
            ],
            user: {
                id: 0,
                name: "",
                lastName: "",
                username: "",
                tDocument: null,
                document: "",
                address: "",
                phone: "",
                email: "",
                password: "",
                roles: []
            },
            submitStatus: null
        };
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key };
                });
        },
        getActiveUsers() {
            return this.$store.state.u.activeUsers;
        },
        getActiveUsersCount() {
            return this.$store.getters.activeUsersCount;
        },
        getActiveDoctypes() {
            return this.$store.state.u.activeDoctypes;
        },
        getActiveRoles() {
            return this.$store.state.ro.activeRoles;
        },
        activeUsersIsLoading() {
            return this.$store.state.u.isLoadingUsers;
        },
        disabledButtonCreateUser() {
            if (this.user.name && this.user.lastName) {
                return true;
            } else {
                return false;
            }
        },
        getErrorUsers() {
            return this.$store.state.u.usersErrors;
        },
        clearFields() {
            if (!this.$store.state.u.isCreatingUsers) {
                this.clearFormFields();
            }
        }
    },
    watch: {
        onLine(v) {
            if (v) {
                this.showBackOnline = true;
                setTimeout(() => {
                    this.showBackOnline = false;
                }, 1000);
            }
        }
    },
    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        showModal(modal) {
            this.$refs[`${modal}`].show();
        },
        hideModal(modal) {
            this.$refs[`${modal}`].hide();
        },
        onRowSelected(item) {
            this.$refs["modal-edit-user"].show();
            this.user.id = item[0].id ? item[0].id : 0;
            this.user.name = item[0].name;
            this.user.lastName = item[0].lastName;
            this.user.username = item[0].username;
            this.user.tDocument = item[0].doctype_id;
            this.user.document = item[0].document;
            this.user.address = item[0].address;
            this.user.phone = item[0].phone;
            this.user.email = item[0].email;
            this.user.roles = item[0].roles;
        },
        refreshData() {
            this.$store.dispatch("getActiveUsers");
        },
        saveUser() {
            this.$store.dispatch("saveUsers", this.user);
        },
        editUser() {
            this.$store.dispatch("editUser", this.user);
        },
        desactivateUser(user) {},
        clearFormFields() {
            this.user = {
                id: 0,
                name: "",
                lastName: "",
                username: "",
                tDocument: null,
                document: "",
                address: "",
                phone: "",
                email: "",
                password: ""
            };
        }
    },
    mounted() {
        window.addEventListener("online", this.updateOnlineStatus);
        window.addEventListener("offline", this.updateOnlineStatus);
    },
    created() {
        this.$store.dispatch("getActiveUsers");
        this.$store.dispatch("getActiveDoctype");
        this.$store.dispatch("getActiveRoles");
    },
    beforeDestroy() {
        window.removeEventListener("online", this.updateOnlineStatus);
        window.removeEventListener("offline", this.updateOnlineStatus);
    }
};
</script>

<style scoped>
.main-content-permission {
    height: calc(100vh - 155px) !important;
    overflow: hidden;
}

.main-content-permission .card-content {
    height: calc(100vh - 155px) !important;
}

.card-body {
    overflow-y: scroll !important;
}
</style>
