<div class="ft-mgmt-dashboard-overview" data-dashboard-panel="overview">
    <x-orders.workflow-stage-overview
        :stages="$orderStages"
        mode="navigate"
        :show-header="false"
        :navigation-query="$orderStageNavigationQuery"
    />



</div>
