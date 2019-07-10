<template>
    <div class="event-form panel panel-default"
         v-loading="spinner"
         element-loading-text="Loading data...">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="el-icon-edit"></i>
                {{form_title}}
            </h3>
        </div>
        <div class="panel-body">
            <el-form ref="eventForm"
                     :model="event_fields"
                     :rules="validationsRules"
                     label-width="120px">
                <el-form-item prop="name" label="Name">
                    <el-input v-model="event_fields.name"></el-input>
                </el-form-item>

                <el-form-item v-if="!event"
                        label="No. Tickets"
                        prop="tickets" >
                    <el-input v-model="event_fields.tickets">
                    </el-input>
                </el-form-item>

                <el-form-item label="Activity time">
                    <el-col :span="11">
                        <el-date-picker
                                type="date"
                                v-model="event_fields.date"
                                placeholder="Payment date"
                                format="dd/MM/yyyy"
                                value-format="yyyy-MM-dd"
                                @change="setEventDateTime"
                                :picker-options="datePickerOptions"
                                style="width: 100%;">
                        </el-date-picker>
                    </el-col>
                    <el-col class="line text-center" :span="2">-</el-col>
                    <el-col :span="11">
                        <el-time-picker placeholder="Event time"
                                        v-model="event_fields.time"
                                        @change="setEventDateTime"
                                        value-format="H:m:s"
                                        style="width: 100%;"></el-time-picker>
                    </el-col>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="save">Save</el-button>
                </el-form-item>
            </el-form>
        </div>

    </div>
</template>

<script>
    import axios from 'axios';
    import moment from 'moment';
    export default {
        name: "event-form"
        , data(){
            let vm = this;
            let validateTickets = function (rule, value, callback){
                if (value === '') {
                    callback(new Error('Please input No. Tickets'));
                } else {
                    if (Number.isNaN( Number.parseFloat(value) )) {
                        callback(new Error('Please input number'));
                    } else if (parseFloat(value) < 0) {
                        callback(new Error('Amount must be equal or greater than 0'));
                    }
                    else {
                        callback();
                    }
                }
            };
            return {
                spinner: false
                , event_fields:{
                    event_id: 0
                    , name:''
                    , event_date: moment()
                    , tickets:0
                    , date: moment().format('Y-MM-DD')
                    , time: moment().format('H:m:s')
                }
                , validationsRules: {
                    name:[
                        { required: true, message: 'Name can not be null', trigger: 'blur' }
                    ]
                    , event_date:[
                        { required: true, message: 'Date can not be null', trigger: 'blur' }
                    ]
                    , tickets: [
                        {validator: validateTickets, trigger: 'blur'}
                    ]
                }
                , datePickerOptions: {
                    disabledDate: this.disabledDueDate
                }
            }
        }
        , props: {
            event:null
        }
        , computed:{
            form_title:function(){
                return (this.event )? 'Edit Event: '+ this.event.event_id : 'New Event' ;
            }
        }
        , created() {
            this.loadData();
        }
        , methods: {
            loadData(){
                if(this.event){
                    this.event_fields.event_id = this.event.event_id;
                    this.event_fields.name = this.event.name;
                    this.event_fields.event_date = this.event.event_date;
                    this.event_fields.date = moment(this.event.event_date).format('Y-MM-DD');
                    this.event_fields.time = moment(this.event.event_date).format('H:m:s');
                }
            }
            , disabledDueDate(date){
                let yesterday = new Date(new Date().setDate(new Date().getDate()-1));
                return ((date < yesterday));
            }
            , setEventDateTime(){
                this.event_fields.event_date = this.event_fields.date + ' ' + this.event_fields.time;
            }
            , save(){
                let url = '';
                let data={};
                if (this.event){
                    url = '/api/events/' + this.event_fields.event_id + '/update';
                    data={
                        event_id: this.event_fields.event_id
                        , name: this.event_fields.name
                        , event_date: this.event_fields.event_date
                    }
                }else{
                    url = '/api/events/add-new';
                    data={
                        name: this.event_fields.name
                        , event_date: this.event_fields.event_date
                        , tickets:this.event_fields.tickets
                    }
                }
                let self = this;
                this.$refs['eventForm'].validate(function(valid){
                    if (valid) {
                        self.spinner=true;
                        axios({
                            method:'POST'
                            , url: url
                            , data: data
                        })
                            .then(function(response) {
                                // console.log(response.data);
                                // self.pp_items = response.data;
                                self.event_fields.event_id = response.data.event_id;
                                self.event_fields.name = response.data.name;
                                self.event_fields.event_date = response.data.event_date;
                                self.spinner = false;

                                // target_item.id: This only to know if it for add_new or update
                                self.$emit('successSaveEvent');
                            })
                            .catch(e => {
                                self.spinner = false;
                                self.$message({
                                    type:'error'
                                    , message:e.response.data.error.message
                                });
                            });

                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>