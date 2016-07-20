Enact.react_class.CreateCampaign = React.createClass({

    test : 0,

    getInitialState: function(){

        var state = { 
                data    : {
                    subject : '',
                    template : null,
                    variables : {}
                },
                variables : [],
                errors  : {},
                test_loading : false,
                campaign_loading : false
            };

        return state;

    },//getInitialState

    componentDidMount: function(){

        if(this.props.templates.length){
            this.changeTemplate(this.props.templates[0].template);
        }//if

    },//componentDidMount

    changeTemplate: function(template){

        var defaults = _.findWhere(this.props.templates,{ template : template }).variables,
            variables = {};

        defaults.map(function(d){
            variables[d.handle] = (d.default) ? d.default : null;
        });

        this.setState(function(state){
            state.variables = defaults;
            state.data.template = template;
            state.data.variables = variables;
            return state;
        },function(){
            autosize($('textarea'));
        });

    },//changeTemplate

    checkForErrors: function(){

        var that        = this,
            errors      = {};

        if(!this.state.data.subject){
            errors.subject = 'You must give the campaign a subject'; 
        }//if

        if(!this.state.data.template){
            errors.subject = 'You must select a template for the campaign'; 
        }//if

        var required    = _.where(this.state.variables, { required : true });

        required.map(function(r){
            if(r.type == 'number' && !_.isFinite(that.state.data.variables[r.handle])){
                errors[r.handle] = r.name + ' must be a numeric value';
            } else if(that.state.data.variables[r.handle] == null || that.state.data.variables[r.handle] == ''){
                errors[r.handle] = 'You must enter a value for ' + r.name;
            }//el

        });

        this.setState({ errors : errors });

        return _.keys(errors).length;

    },//checkForErrors

    getError: function(key){

        if(this.state.errors[key]){
            return <div className='enact-error'>{this.state.errors[key]}</div>
        }//if

        return null;

    },//getError

    onChange: function(event){

        var name = event.target.getAttribute('name'),
            value = event.target.value;

        if(name == 'template'){
            this.changeTemplate(value);
        } else {
            this.state.data[name] = value;
        }//el

    },//onChange

    onChangeVariable: function(event){

        var name = event.target.getAttribute('name'),
            value = event.target.value;

        this.setState(function(state){
            state.data.variables[name] = value;
            return state;
        });

    },//onChange

    onTest: function(event){
        this.test = 1;
        this.onCreate(event);
    },//onTest

    onCreate: function(event){

        if(this.checkForErrors()){
            Enact.notify.alert('Please fix the errors listed in red and try again',true);
            return;
        }//if

        if(!this.test && !confirm('Are you sure you want to create this campaign? This will email everyone subscribed to your newsletter and cannot be stopped. Make sure you have tested before hand!')){
            return;
        }//if

        var data = {
                data : this.state.data,
                test : this.test
            };

        if(!this.test){
            this.setState({ campaign_loading : true });
        } else {
            this.setState({ test_loading : true });
        }//el

        this.test = 0;

        var that = this;

        Enact.ajax({
            url : 'email-newsletter/campaigns/create',
            type : 'POST',
            data : data,
            success:function(r){

                if(data.test){
                    that.setState({ test_loading : false });
                }//el

                if(!r.e){
                    Enact.notify.alert(r.d);
                    if(!data.test){
                        window.location.href = Enact.cpSlug('email-newsletter/campaigns/');
                    }//if
                } else {
                    Enact.notify.alert(r.d || "Something went wrong, unable to send campaign. Make sure your email is configured properly.",true);
                }//el

            }//success
        });

    },//onCreate
    
    render: function(){

        var that        = this,
            templates   = [],
            variables   = [];

        this.props.templates.map(function(t,i){
            templates.push(<option value={t.template} key={i}>{t.template}</option>);
        });

        if(this.state.data.template && this.state.variables.length){
            this.state.variables.map(function(variable,i){
                var tip = (variable.tip) ? <small>{variable.tip}</small> : null,
                    input;

                if(variable.type == 'textarea'){
                    input = <textarea name={variable.handle} onChange={that.onChangeVariable} value={that.state.data.variables[variable.handle]}/>;
                } else {
                    input = <input type={variable.type} name={variable.handle} onChange={that.onChangeVariable} value={that.state.data.variables[variable.handle]}/>
                }//el

                variables.push((
                        <div key={i}>
                            <label>{variable.name}</label>
                            {tip}
                            {input}
                            {that.getError(variable.handle)}
                        </div>
                    ));
            });
        }//if

        return (
                <div>
                    <div className='row heading-con'>
                        <div className='small-12 medium-6 columns'>
                            <h4>Create Campaign</h4>
                        </div>
                        <div className='small-12 medium-6 columns text-right mobile-text-center button-group'>
                            <Enact.react_class.ButtonWithLoad
                                text='Test'
                                loading={this.state.test_loading}
                                onClick={this.onTest}
                            />
                            <Enact.react_class.ButtonWithLoad
                                text='Create & Send'
                                loading={this.state.campaign_loading}
                                onClick={this.onCreate}
                            />
                        </div>
                    </div>
                    <div className='content-con'>
                        <label>Subject</label>
                        <small>The subject line of the campaign email</small>
                        <input type='text' name='subject' onChange={this.onChange}/>
                        {this.getError('subject')}
                        <label>Template</label>
                        <small>The template used for the body of the campaign email, if you have no templates created talk to your developer</small>
                        <select name='template' onChange={this.onChange}>
                            {templates}
                        </select>
                        {this.getError('template')}

                        {variables}
                    </div>

                </div>
            );

    }//render

});
