<template>
    <div class="main-content-permission">
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-container fluid>
            <b-row>
                <b-col cols="12">
                    <b-card header-tag="header"
                            no-body
                            border-variant="secondary"
                            header-border-variant="secondary"
                            class="card-content"
                    >

                        <template #header>
                            <b-row>
                                <b-col xl="2"
                                       offset-xl="10"
                                       class="text-right"
                                >
                                    <b-button variant="warning"
                                              size="sm"
                                              pill
                                              title="Recargar"
                                              @click="refreshData"
                                              class="button-times-icon"
                                    >
                                        <i class="fas fa-sync-alt times-icon"></i>
                                    </b-button>

                                    <!-- BUTTON CREATE PERMISSION -->
                                    <b-button variant="primary"
                                              size="sm"
                                              pill
                                              title="Crear Nuevo Rol"
                                              @click="showModal('modal-new-role')"
                                              class="button-times-icon"
                                    >
                                        <i class="fas fa-plus times-icon"></i> Crear Rol
                                    </b-button>

                                </b-col>
                            </b-row>
                        </template>

                        <b-card-body>
                            <b-row>
                                <b-col xl="12" class="my-1">
                                    <b-table
                                        :items="getActiveRoles"
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
                                        :busy="activeRolesIsLoading"
                                        @row-selected="onRowSelected"
                                    >

                                        <template #table-busy>
                                            <div class="text-center text-danger my-2">
                                                <b-spinner class="align-middle"></b-spinner>
                                                <strong>Loading...</strong>
                                            </div>
                                        </template>

                                        <template #cell(permissions)="row" class="text-left">
                                            <b-badge variant="primary" size="sm" class="m-1" v-for="(item, index) in row.value" :key="index">
                                                {{ item.name }}
                                            </b-badge>
                                        </template>

                                        <template #cell(actions)="row">
                                            <b-button pill size="sm" variant="danger" title="Desactivar Rol" @click="desactivateRole(row)">
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
                                        :total-rows="getActiveRolesCount"
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
                                    total de registros: {{ getActiveRolesCount }}
                                </b-col>
                            </b-row>
                        </template>

                    </b-card>
                </b-col>
            </b-row>
        </b-container>

        <!-- MODALS FROM THIS COMPONENT -->
        <!-- CREATE PERMISSION -->
        <b-modal ref="modal-new-role"
                 no-close-on-backdrop
                 no-close-on-esc
        >
            <template #modal-header>
                <p class="m-0">CREAR ROL</p>
            </template>

            <template #default>
                <b-container fluid class="mt-2">
                    <b-row>
                        <b-col cols="12">
                            <b-form autocomplete="off">
                                <b-form-group id="input-group-1"
                                              label="Nombre del rol:"
                                              label-for="input-1"
                                >
                                    <b-form-input
                                        id="input-1"
                                        v-model="role.name"
                                        type="text"
                                        placeholder="Ingrese el nombre del Rol"
                                        required
                                    ></b-form-input>

                                    <span class="text-danger" v-for="(item, index) in getErrors" :key="index">
                                        {{ item }}
                                    </span>

                                </b-form-group>
                            </b-form>
                        </b-col>
                    </b-row>
                </b-container>
            </template>

            <template #modal-footer>
                <b-button pill
                          variant="danger"
                          @click="hideModal('modal-new-role')"
                          class="button-times-icon"
                >
                    <i class="fas fa-times"></i> Cancelar
                </b-button>
                <b-button pill
                          variant="success"
                          @click="savePermission()"
                          :disabled="disabledButtonCreateRole"
                          class="button-times-icon"
                >
                    <i class="fas fa-save"></i> Guardar
                </b-button>
            </template>

        </b-modal>

        <!-- EDIT PERMISSION -->
        <b-modal ref="modal-edit-role"
                 no-close-on-backdrop
                 no-close-on-esc
        >

            <template #modal-header>
                <p class="m-0">EDITAR ROL</p>
            </template>

            <template #default>
                <b-container fluid class="mt-3">
                    <b-row>
                        <b-col cols="12">
                            <b-form autocomplete="off">

                                <b-form-group id="input-group-2"
                                              label="Nombre del Rol:"
                                              label-for="input-2"
                                >
                                    <b-form-input
                                        id="input-2"
                                        v-model="role.name"
                                        type="text"
                                        placeholder="Edite el nombre del rol"
                                        required
                                    ></b-form-input>

                                </b-form-group>

                                <b-form-group id="input-group-3"
                                              label="Guard Name del Rol:"
                                              label-for="input-3"
                                >
                                    <b-form-input
                                        id="input-3"
                                        v-model="role.guardName"
                                        type="text"
                                        placeholder="Guard name del rol"
                                        disabled
                                        required
                                    ></b-form-input>

                                </b-form-group>

                                <b-form-group id="input-group-4"
                                              label="Fecha Creación:"
                                              label-for="input-4"
                                >
                                    <b-form-input
                                        id="input-4"
                                        v-model="role.createdAt"
                                        type="text"
                                        placeholder="Fecha de creación del rol"
                                        disabled
                                        required
                                    ></b-form-input>

                                </b-form-group>

                                <b-form-group>
                                    <label for="permissions-select-input">Permisos:</label>
                                    <v-select multiple v-model="role.permissions"
                                              :reduce="permissions => permissions.id"
                                              :options="getActivePermissions"
                                              label="name"
                                              placeholder="Seleccionar Permisos"
                                              id="permissions-select-input"
                                    />
                                </b-form-group>

                            </b-form>
                        </b-col>
                    </b-row>
                </b-container>
            </template>

            <template #modal-footer>
                <b-button pill
                          variant="danger"
                          @click="hideModal('modal-edit-role')"
                          class="button-times-icon"
                >
                    <i class="fas fa-times times-icon"></i> Cancelar
                </b-button>
                <b-button pill
                          variant="warning"
                          @click="editRoleWithPermission()"
                          :disabled="disabledButtonCreateRole"
                          class="button-times-icon"
                >
                    <i class="fas fa-pen"></i> Editar
                </b-button>
            </template>

        </b-modal>
    </div>
</template>

<script>

import NoConnectionAlert from "../../shared/alerts/NoConnectionAlert"

export default {
    name: "Roles",
    components: {
        NoConnectionAlert
    },
    data() {
        return {
            onLine: navigator.onLine,
            selectMode: 'single',
            selected: [],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            fields: [
                { key: 'id', label: 'Id', class: 'text-center' },
                { key: 'name', label: 'Name', class: 'text-center' },
                { key: 'guard_name', label: 'Guard', class: 'text-center' },
                { key: 'permissions', label: 'Permisos', class: 'text-left' },
                { key: 'created_at', label: 'Fecha Creación', class: 'text-center' },
                { key: 'actions', label: 'Acciones', class: 'text-center' }
            ],
            role: {
                id: '',
                name: '',
                guardName: '',
                createdAt: '',
                permissions: []
            }
        }
    },
    computed: {
        getActiveRoles() {
            return this.$store.state.ro.activeRoles
        },
        getActivePermissions() {
            return this.$store.state.p.activePermissions
        },
        getActiveRolesCount() {
            return this.$store.getters.activeRolesCount
        },
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
        },
        activeRolesIsLoading() {
            return this.$store.state.ro.isLoadingRoles
        },
        disabledButtonCreateRole() {
            if (!this.role.name) {
                return true;
            }

            return false;
        },
        getErrors() {
            if (this.$store.state.ro.rolesErrors.length > 0) {
                if (this.role.name) {
                    return this.$store.state.ro.rolesErrors
                }
            }
        },
    },
    watch: {
        onLine(v) {
            if (v) {
                this.showBackOnline = true
                setTimeout(() => {
                    this.showBackOnline = false;
                }, 1000)
            }
        }
    },
    methods: {
        showModal(modal) {
            this.$refs[`${modal}`].show()
        },
        hideModal(modal) {
            this.$refs[`${modal}`].hide()
        },
        onRowSelected(item) {
            this.$refs['modal-edit-role'].show()
            this.role.id = item[0].id != null ? item[0].id : ''
            this.role.name = item[0].name
            this.role.guardName = item[0].guard_name
            this.role.createdAt = item[0].created_at
            this.role.permissions = item[0].permissions
        },
        refreshData() {
            this.$store.dispatch('getActiveRoles')
        },
        savePermission() {

            if (!this.role.name) {
                window.alert('Debe digitar un nombre para el rol')

            } else {
                this.$store.dispatch('createNewRol', this.role)
            }
        },
        editRoleWithPermission() {
            if (!this.role.name || this.role.permissions.length === 0) {
                window.alert('Campo Rol Vacio O no ha Seleccionado Permisos para agregar al Rol')
            } else {
                this.$store.dispatch('editRole', this.role)
            }

        },
        clearFields() {
            this.role.name = ''
        },
        desactivateRole(role) {
            this.$store.dispatch('deleteRole', role.item)
        }
    },
    mounted() {
        window.addEventListener('online', this.updateOnlineStatus)
        window.addEventListener('offline', this.updateOnlineStatus)
    },
    created() {
        this.$store.dispatch('getActiveRoles')
        this.$store.dispatch('getActivePermissions')
    },
    beforeDestroy() {
        window.removeEventListener('online', this.updateOnlineStatus)
        window.removeEventListener('offline', this.updateOnlineStatus)
    }
}
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
