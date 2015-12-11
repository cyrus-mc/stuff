module MCollective
        module Agent
                class Subscriptionmanager<RPC::Agent

			activate_when do
				File.executable?("/usr/bin/subscription-manager")
			end

                        action "attach" do

                                runcmd = "subscription-manager attach --pool=#{request[:pool]}"

                                reply[:status] = run(runcmd, :stdout => :out, :stderr => :err)
                        end


                        action "refresh" do
                                runcmd = "subscription-manager refresh"

                                reply[:status] = run(runcmd, :stdout => :out, :stderr => :err)
                        end
                end
        end
end
