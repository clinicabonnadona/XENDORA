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
                        class="card-content"
                    >
                        <template #header>
                            <b-row>
                                <b-col xl="2" offset-xl="10" class="text-right">
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

                                    <!-- BUTTON CREATE PERMISSION -->
                                    <b-button
                                        variant="primary"
                                        size="sm"
                                        pill
                                        title="Crear Nuevo Permiso"
                                        @click="
                                            showModal('modal-new-permission')
                                        "
                                        class="button-times-icon"
                                    >
                                        <i class="fas fa-plus times-icon"></i>
                                        Crear Permiso
                                    </b-button>
                                </b-col>
                            </b-row>
                        </template>

                        <b-card-body>
                            <b-row>
                                <b-col xl="12" class="my-1">
                                    <b-table
                                        :items="getActivePermissions"
                                        :fields="fields"
                                        :current-page="currentPage"
                                        :per-page="perPage"
                                        :select-mode="selectMode"
                                        responsive
                                        hover
                                        bordered
                                        ref="selectableTable"
                                        selectable
                                        head-variant="dark"
                                        small
                                        show-empty
                                        :busy="activePermissionIsLoading"
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

                                        <template #cell(actions)="row">
                                            <b-button
                                                pill
                                                size="sm"
                                                variant="danger"
                                                title="Desactivar Permiso"
                                                @click="
                                                    desactivatePermission(row)
                                                "
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
                                        :total-rows="getActivePermissionsCount"
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
                                    {{ getActivePermissionsCount }}
                                </b-col>
                            </b-row>
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>

        <!-- MODALS FROM THIS COMPONENT -->
        <!-- CREATE PERMISSION -->
        <b-modal
            ref="modal-new-permission"
            no-close-on-backdrop
            no-close-on-esc
        >
            <template #modal-header>
                <p class="m-0">CREAR PERMISO</p>
            </template>

            <template #default>
                <b-container fluid class="mt-2">
                    <b-row>
                        <b-col cols="12">
                            <b-form autocomplete="off">
                                <b-form-group
                                    id="input-group-1"
                                    label="Nombre del Permiso:"
                                    label-for="input-1"
                                >
                                    <b-form-input
                                        id="input-1"
                                        v-model="permission.name"
                                        type="text"
                                        placeholder="Ingrese el nombre del permiso"
                                        required
                                    ></b-form-input>

                                    <span
                                        class="text-danger"
                                        v-for="(item, index) in getErrors"
                                        :key="index"
                                    >
                                        {{ item }}
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
                    @click="hideModal('modal-new-permission')"
                    class="button-times-icon"
                >
                    <i class="fas fa-times times-icon"></i> Cancelar
                </b-button>
                <b-button
                    pill
                    variant="success"
                    @click="savePermission()"
                    :disabled="disabledButtonCreatePermission"
                    class="button-times-icon"
                >
                    <i class="fas fa-save times-icon"></i> Guardar
                </b-button>
            </template>
        </b-modal>

        <!-- EDIT PERMISSION -->
        <b-modal
            ref="modal-edit-permission"
            no-close-on-backdrop
            no-close-on-esc
        >
            <template #modal-header>
                <p class="m-0">EDITAR PERMISO</p>
            </template>

            <template #default>
                <b-container fluid class="mt-3">
                    <b-row>
                        <b-col cols="12">
                            <b-form autocomplete="off">
                                <b-form-group
                                    id="input-group-2"
                                    label="Nombre del Permiso:"
                                    label-for="input-2"
                                >
                                    <b-form-input
                                        id="input-2"
                                        v-model="permission.name"
                                        type="text"
                                        placeholder="Edite el nombre del permiso"
                                        required
                                    ></b-form-input>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-3"
                                    label="Guard Name del Permiso:"
                                    label-for="input-3"
                                >
                                    <b-form-input
                                        id="input-3"
                                        v-model="permission.guardName"
                                        type="text"
                                        placeholder="Guard name del Permiso"
                                        disabled
                                        required
                                    ></b-form-input>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-4"
                                    label="Fecha Creación:"
                                    label-for="input-4"
                                >
                                    <b-form-input
                                        id="input-4"
                                        v-model="permission.createdAt"
                                        type="text"
                                        placeholder="Fecha de creación del permiso"
                                        disabled
                                        required
                                    ></b-form-input>
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
                    @click="hideModal('modal-edit-permission')"
                    class="button-times-icon"
                >
                    <i class="fas fa-times times-icon"></i> Cancelar
                </b-button>
                <b-button
                    pill
                    variant="warning"
                    @click="savePermission()"
                    :disabled="disabledButtonCreatePermission"
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

export default {
    name: "Permissions",
    components: {
        NoConnectionAlert
    },
    data() {
        return {
            onLine: navigator.onLine,
            selectMode: "single",
            selected: [],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            fields: [
                { key: "id", label: "Id", class: "text-center" },
                { key: "name", label: "Name", class: "text-center" },
                { key: "guard_name", label: "Guard", class: "text-center" },
                {
                    key: "created_at",
                    label: "Fecha Creación",
                    class: "text-center"
                },
                { key: "actions", label: "Acciones", class: "text-center" }
            ],
            permission: {
                id: "",
                name: "",
                guardName: "",
                createdAt: ""
            }
        };
    },
    computed: {
        getActivePermissions() {
            return this.$store.state.p.activePermissions;
        },
        getActivePermissionsCount() {
            return this.$store.getters.activePermissionsCount;
        },
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key };
                });
        },
        activePermissionIsLoading() {
            return this.$store.state.p.isLoadingPermissions;
        },
        disabledButtonCreatePermission() {
            if (!this.permission.name) {
                return true;
            } else {
                return false;
            }
        },
        getErrors() {
            if (this.$store.state.p.permissionErrors.length > 0) {
                if (this.permission.name) {
                    return this.$store.state.p.permissionErrors;
                }
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
        showModal(modal) {
            this.$refs[`${modal}`].show();
        },
        hideModal(modal) {
            this.$refs[`${modal}`].hide();
        },
        onRowSelected(item) {
            this.$refs["modal-edit-permission"].show();
            this.permission.id = item[0].id;
            this.permission.name = item[0].name;
            this.permission.guardName = item[0].guard_name;
            this.permission.createdAt = item[0].created_at;
        },
        refreshData() {
            this.$store.dispatch("getActivePermissions");
        },
        savePermission() {
            if (!this.permission.name) {
                window.alert("Debe digitar un nombre para el permiso");
            } else {
                this.$store.dispatch("savePermission", this.permission);
            }
        },
        clearFields() {
            this.permission.name = "";
        },
        desactivatePermission(permission) {
            this.$store.dispatch("desactivatePermission", permission.item);
        }
    },
    mounted() {
        window.addEventListener("online", this.updateOnlineStatus);
        window.addEventListener("offline", this.updateOnlineStatus);
    },
    created() {
        this.$store.dispatch("getActivePermissions");
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
