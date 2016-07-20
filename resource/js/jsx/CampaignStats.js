Enact.react_class.CampaignStats = React.createClass({displayName: "CampaignStats",

    stats : {},

    componentWillMount: function(){

        this.stats.view_perc                = ((this.props.campaign.total_viewed / this.props.campaign.total_sent) * 100).toFixed(2);
        this.stats.click_through_perc       =  ((this.props.campaign.total_click_through / this.props.campaign.total_sent) * 100).toFixed(2);
        this.stats.view_click_through_perc  =  ((this.props.campaign.total_click_through / this.props.campaign.total_viewed) * 100).toFixed(2);
        
    },//componentWillMount

    componentDidMount: function(){

        var $that           = $(ReactDOM.findDOMNode(this)),
            margin          = {top: 10, right: 20, bottom: 30, left: 40},

            max_view        = _.max(this.props.stats, 'viewed_on').viewed_on,
            max_click       = _.max(this.props.stats, 'clicked_through_on').clicked_through_on,
            max_time        = (max_view > max_click) ? max_view : max_click,

            views           = _.sortBy(_.filter(this.props.stats, function(s){ return s.viewed_on != null; }),'viewed_on'),
            clicks          = _.sortBy(_.filter(this.props.stats, function(s){ return s.clicked_through_on != null; }),'clicked_through_on');

            width           = $that.width() * .9 - margin.left - margin.right,
            height          = (width * .25) - margin.top - margin.bottom,
            ticks           = 8,

            interpolation   = 'cardinal-open',

            fontSize        = '12px',

            ele     = this.refs['chart'],

            x       = d3.time.scale().range([0,width]),

            y       = d3.scale.linear().range([height, 0]),

            xAxis   = d3.svg.axis()
                        .scale(x)
                        .ticks(ticks)
                        .orient("bottom"),

            yAxis   = d3.svg.axis()
                        .scale(y)
                        .ticks(3)
                        .orient("left"),

            view_line = d3.svg.line()
                        .interpolate(interpolation)
                        .x(function(s) { return x(s.viewed_on); })
                        .y(function(s,i) { return y(i+1); }),

            click_line = d3.svg.line()
                        .interpolate(interpolation)
                        .x(function(s) { return x(s.clicked_through_on); })
                        .y(function(s,i) { return y(i+1); }),

            svg     = d3.select(ele).append("svg")
                        .attr("width", width + margin.left + margin.right)
                        .attr("height", height + margin.top + margin.bottom);

            
        svg = svg.append('g')
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


        views.map(function(s){
            s.viewed_on = new Date(s.viewed_on * 1000);
        });

        clicks.map(function(s){
            s.clicked_through_on = new Date(s.clicked_through_on * 1000);
        });


        x.domain([new Date(this.props.campaign.created_on_unix * 1000), new Date(max_time * 1000)]);
        y.domain([0,this.props.campaign.total_sent]);


        svg.append("path")
            .datum(views)
            .attr("class", "viewed-on-line")
            .attr("d", view_line);

        svg.append("path")
            .datum(clicks)
            .attr("class", "clicked-on-line")
            .attr("d", click_line);

         svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .attr('font-size',fontSize)
            .call(xAxis)

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis)
            .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 2)
            .attr("dy", ".71em");

    },//componentDidMount

    render : function(){

        return (
                React.createElement("div", null, 
                    React.createElement("div", {className: "heading-con row"}, 
                        React.createElement("h1", null, "Campaign Stats | #", this.props.campaign.id)
                    ), 
                    React.createElement("div", {className: "content-con"}, 
                        React.createElement("div", null, React.createElement("b", null, "Subject : "), " ", React.createElement("small", null, this.props.campaign.subject)), 
                        React.createElement("div", null, React.createElement("b", null, "Created : "), " ", React.createElement("small", null, Enact.formatDate(this.props.campaign.created_on_unix))), 

                        React.createElement("table", null, 
                            React.createElement("thead", null, 
                                React.createElement("tr", null, 
                                    React.createElement("td", null, "Total Sent"), 
                                    React.createElement("td", null, "Total Viewed"), 
                                    React.createElement("td", null, "% Viewed"), 
                                    React.createElement("td", null, "Total Click Through"), 
                                    React.createElement("td", null, "% Clicked Through"), 
                                    React.createElement("td", null, "% Viewed & Clicked Through")
                                )
                            ), 
                            React.createElement("tbody", null, 
                                React.createElement("tr", {style: { textAlign : 'right'}}, 
                                    React.createElement("td", null, this.props.campaign.total_sent), 
                                    React.createElement("td", null, this.props.campaign.total_viewed), 
                                    React.createElement("td", null, this.stats.view_perc), 
                                    React.createElement("td", null, this.props.campaign.total_click_through), 
                                    React.createElement("td", null, this.stats.click_through_perc), 
                                    React.createElement("td", null, this.stats.view_click_through_perc)
                                )
                            )
                        ), 

                        React.createElement("div", {className: "text-center"}, 

                            React.createElement("div", {style: { display : 'inline-block'}}, 
                                React.createElement("span", {style: { display: 'inline-block', backgroundColor : '#2EBBFF', width : 10, height : 10}}), 
                                "Views"
                            ), 

                            React.createElement("div", {style: { display : 'inline-block', marginLeft : 10}}, 
                                React.createElement("span", {style: { display: 'inline-block', backgroundColor : '#21FFCF', width : 10, height : 10}}), 
                                "Clicks"
                            )

                        ), 

                        React.createElement("div", {ref: "chart"})

                    )
                )
            );

    }//render

});
