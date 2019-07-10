<style scoped>

</style>
<template>
    <div class="events-list container">
        <div class="card card-default">
            <div class="card-header">
                <el-row :gutter="20">
                    <el-col :span="18">
                        <el-page-header @back="goBack" content="All Events">
                        </el-page-header>
                    </el-col>
                    <el-col :span="6" class="text-right">
                        <el-button @click="loadForm()" type="primary" icon="el-icon-circle-plus"  size="small" >Add New Event</el-button>
                        <el-button @click="refresh()" type="primary" icon="el-icon-refresh"  size="small" >Refresh</el-button>
                    </el-col>

                </el-row>
            </div>
            <div class="card-body">


                <el-row :gutter="20">
                    <vuetable ref="vuetable"
                              v-loading="spinner"
                              element-loading-text="Loading data from Server..."
                              api-url="/api/events"

                              pagination-path="meta"
                              :fields="events_fields"
                              :css="css"
                              :sort-order="sortOrder"
                              :per-page="10"
                              @vuetable:pagination-data="onPaginationData"
                              @vuetable:loading="vuetableLoading"
                              @vuetable:load-success="vuetableLoadSuccess"
                    >
                        <template slot="tickets" slot-scope="props">
                            <el-tooltip class="item" effect="dark" content="View Tickets" placement="top">
                                <router-link :to="getLink('tickets',props.rowData.event_id)">
                                    <el-button type="primary" icon="el-icon-search" circle  size="mini"></el-button>
                                </router-link>
                            </el-tooltip>
                        </template>

                        <template slot="actions" slot-scope="props">
                            <el-tooltip class="item" effect="dark" content="Edit Event" placement="top">
                                <el-button @click="loadForm(props.rowData)" type="warning" icon="el-icon-edit"  size="small" >Edit</el-button>
                            </el-tooltip>
                        </template>
                    </vuetable>
                    <div class="vuetable-footer">
                        <div class="justify-content-end">
                            <vuetable-pagination-info ref="paginationInfo"></vuetable-pagination-info>
                        </div>

                        <vuetable-pagination-bootstrap ref="pagination"
                                                       @vuetable-pagination:change-page="onChangePage"
                        ></vuetable-pagination-bootstrap>
                    </div>
                </el-row>
            </div>
        </div>
        <el-dialog title="Event Form" :visible.sync="showForm"  :close-on-click-modal="false" append-to-body>
            <event-form v-if="showForm"
                              :event="target_event"
                        @successSaveEvent="saveEvent"
            ></event-form>
        </el-dialog>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePaginationBootstrap from '../common/VuetablePaginationBootstrap'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    import BootstrapStyle from '../common/bootstrap-css.js'
    import eventForm from './Form.vue'
    export default {
        name: "events-list"
        , components: {
            Vuetable
            , VuetablePaginationBootstrap
            , VuetablePaginationInfo
            , eventForm
        }
        , data(){
            return{
                spinner: true
                , showForm: false
                , css: BootstrapStyle
                , target_event :{}
                , events_fields:[
                    {
                        name: 'event_id',
                        title: 'ID',
                        titleClass: 'text-center',
                        dataClass: 'text-right',
                        sortField: 'event_id'
                    }
                    , {
                        name: 'name',
                        title: 'Name',
                        titleClass: 'text-left',
                        dataClass: 'text-left',
                        sortField: 'name'
                    }
                    , {
                        name: 'event_date',
                        title: 'Date',
                        titleClass: 'text-left',
                        dataClass: 'text-left',
                        sortField: 'event_date'
                    }
                    , {
                        name: 'number_of_tickets',
                        title: 'Tickets',
                        titleClass: 'text-left',
                        dataClass: 'text-left'
                    }
                    , {
                        name: 'number_of_ok',
                        title: 'Active',
                        titleClass: 'text-left',
                        dataClass: 'text-left'
                    }
                    , {
                        name: 'number_of_redeemed',
                        title: 'Redeemed',
                        titleClass: 'text-left',
                        dataClass: 'text-left'
                    }
                    ,{
                        name: 'updated_at',
                        title: 'Updated at',
                        titleClass: 'text-left',
                        dataClass: 'text-left',
                        sortField: 'updated_at'
                    }
                    ,{
                        name: '__slot:tickets',
                        title: 'Tickets',
                        titleClass: 'text-center',
                        dataClass: 'text-center',

                    }
                    ,{
                        name: '__slot:actions',
                        title: '',
                        titleClass: 'text-center',
                        dataClass: 'text-center',

                    }
                ]
                , sortOrder: [
                    {
                        field: 'event_date',
                        sortField: 'event_date',
                        direction: 'asc'
                    }
                ]
            }
        }
        , methods:{
            vuetableLoadSuccess(response) {
                this.spinner = false;
            }
            , vuetableLoading() {
                this.spinner = true;
            }
            , onPaginationData(paginationData) {
                // console.log('onPaginationData' , paginationData);
                this.$refs.pagination.setPaginationData(paginationData)
                this.$refs.paginationInfo.setPaginationData(paginationData)
            }
            , onChangePage (page) {
                // this.loadData(this.url + '?page='+ page);
                this.$refs.vuetable.changePage(page)
            }
            , getLink(action,id){
                if(action == 'tickets'){
                    return '/events/'+ id + '/tickets';
                }
                if(action == 'edit'){
                    return '/events/edit/'+ id;
                }
            }
            , goBack(){
                this.$router.push('/');
            }
            , loadForm(event=null){
                this.target_event =event;
                this.showForm = true;
            }
            , saveEvent(){
                this.showForm= false;
                this.refresh();
                this.$message({
                    type: 'success',
                    message: 'Success update event'
                });
            }
            , refresh(){
                let self = this;
                this.spinner = true;
                this.$refs.vuetable.reload()
                    .then(function () {
                        self.spinner = false;
                    })
                    .catch(e => {
                        self.spinner = false;
                        self.$message({
                            type:'error'
                            , message:e.response.data.error.message
                        });
                    });
            }
        }
    }
</script>

