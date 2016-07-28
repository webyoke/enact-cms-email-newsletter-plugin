Enact.react_class.EmailSubscriptions = React.createClass({displayName: "EmailSubscriptions",

    mixins: [Enact.react_class.DataFeedAdapter],

    does_not_use_groups : true,

    bootstrap_url : 'email-newsletter/subscriptions/data',

    component_name : 'EmailSubscriptions',

    default_sort : 'created_on',

    searchable : ['email'],

    headings : [
        { key : 'email', title : 'Email', class : 'small-6' },
        { key : 'ip_address', title : 'IP', class : 'small-2' },
        { key : 'created_on', title : 'Created', class : 'small-2' },
        { key : '', title : '', class : 'small-2' },
    ],

    componentMounted: function(){

        ReactDOM.render(
            React.createElement(Enact.react_class.ButtonWithLoad, {
                text: "Export", 
                title: "Export and download email subscriptions in CSV format", 
                onClick: this.onExport, 
                loading: this.state.loading_export}
            ),
            document.getElementById('export-button')
        );

    },//componentMounted

    onExport: function(){

        var that = this;

        this.setState({ export_loading : true });
        window.location.href = Enact.cpSlug('email-newsletter/subscriptions/export');
        this.setState({ export_loading : false });

    },//onExport

    render: function(){

        var that = this,
            subscriptions = [];

        this.state.data.map(function(sub,i){
            subscriptions.push(React.createElement(Enact.react_class.SingleEmailSubscription, {sub: sub, owner: that, key: sub.id}));
        });

        return this.getBasicRender({
            data : subscriptions,
            noData : 'No one has subscribed yet!',
            noSearchData : 'No subscriptions found!',
            headings : this.headings
        });

    }//render

});


Enact.react_class.SingleEmailSubscription = React.createClass({displayName: "SingleEmailSubscription",

    onRemove: function(){

        if(!confirm('Are you sure you want to remove {0} from the email subscriptions list?'.format(this.props.sub.email))){
            return;
        }//if

        var that = this;
        Enact.ajax({
            url: 'email-newsletter/subscriptions/' + this.props.sub.id,
            type: 'DELETE',
            success: function(r){
                if(!r.e){
                    that.props.owner.removeDatum(that.props.sub);
                    Enact.notify.alert('Removed!');
                } else {
                    Enact.notify.alert('Could not remove subscription!',true);
                }//el
            }//success
        });
    },//onRemove

    render: function(){
        return (
                React.createElement("div", {className: "row datum-row"}, 
                    React.createElement("div", {className: "small-12 medium-6 columns"}, 
                        this.props.sub.email
                    ), 
                    React.createElement("div", {className: "small-12 medium-2 columns"}, 
                        this.props.sub.ip_address
                    ), 
                    React.createElement("div", {className: "small-12 medium-2 columns"}, 
                        Enact.formatDate(this.props.sub.created_on)
                    ), 
                    React.createElement("div", {className: "small-2 columns text-right"}, 
                        React.createElement("div", {className: "button warning fi-x icon", onClick: this.onRemove, title: "Remove subscriber"})
                    )
                )
            );
    }//render

});
