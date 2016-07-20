Enact.react_class.Campaigns = React.createClass({

    mixins: [Enact.react_class.DataFeedAdapter],

    does_not_use_groups : true,

    bootstrap_url : 'email-newsletter/campaigns/data',

    component_name : 'Campaigns',

    default_sort : 'created_on',

    searchable : ['subject'],

    headings : [
        { key : 'subject', title : 'Subject', class : 'small-5' },
        { key : 'template', title : 'Template', class : 'small-2' },
        { key : 'total_sent', title : 'Total', class : 'small-1' },
        { key : 'total_viewed', title : 'Viewed', class : 'small-1' },
        { key : 'total_click_through', title : 'Clicked', class : 'small-1' },
        { key : 'created_on', title : 'Created', class : 'small-2' },
    ],

    render: function(){

        var that = this,
            campaigns = [];

        this.state.data.map(function(campaign,i){
            campaigns.push(<Enact.react_class.SingleCampaign campaign={campaign} owner={that} key={i + 'k' + campaign.id}/>);
        });

        return this.getBasicRender({
            data : campaigns,
            noData : 'No campaigns created yet!',
            noSearchData : 'No campaigns found!',
            headings : this.headings
        });

    }//render

});


Enact.react_class.SingleCampaign = React.createClass({

    render: function(){
        return (
                <div className='row datum-row'>
                    <div className='small-12 medium-5 columns'>
                        <a href={Enact.cpSlug('email-newsletter/campaigns/' + this.props.campaign.id)}>{this.props.campaign.subject}</a>
                    </div>
                    <div className='small-12 medium-2 columns'>
                        {this.props.campaign.template}
                    </div>
                    <div className='small-12 medium-1 columns'>
                        {this.props.campaign.total_sent}
                    </div>
                    <div className='small-12 medium-1 columns'>
                        {this.props.campaign.total_viewed}
                    </div>
                    <div className='small-12 medium-1 columns'>
                        {this.props.campaign.total_click_through}
                    </div>
                    <div className='small-12 medium-2 columns'>
                        {Enact.formatDate(this.props.campaign.created_on)}
                    </div>
                </div>
            );
    }//render

});
