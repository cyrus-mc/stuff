metadata :name          => "RedHat subscription-manager",
         :description   => "Use subscription-manager utility to manage subscriptions to products",
         :author        => "Matthew Ceroni",
         :license       => "",
         :url           => "http://8x8.com",
         :version       => "0.1",
         :timeout       => 30

action "attach", :description => "Attach subscription" do
        display :always

        input :pool,
                :prompt         => "Pool ID",
                :description    => "ID for the subscription to attach to system",
                :type           => :string,
                :validation     => '^[a-zA-Z0-9]+$',
                :optional       => false,
                :maxlength      => 32

        output :status,
                :description    => "subscription-manager attach status/result",
                :display_as     => "Status/Result"
end


action "refresh", :description => "Pull latest entitlement data from the server" do
        display :always

        output :status,
                :description    => "subscription manager refresh status/result",
                :display_as     => "Status/Result"
end
