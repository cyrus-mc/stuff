Name:		mcollective
Version:	2.8.1
Release:	2%{?dist}
Summary:	Application server for hosting Ruby code on any capable middleware

Group:		System Environment/Base
License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Source0:	http://downloads.puppetlabs.com/mcollective/mcollective-2.8.1.tar.gz
BuildRoot:	%(mktemp -ud %{_tmppath}/%{name}-%{version}-%{release}-XXXXXX)

BuildRequires:	ruby2 >= 2.2.2
Requires: 	ruby2 >= 2.2.2, mcollective-common


%description
The Marionette Collective AKA mcollective is a framework to build server orchestration or
parallel job execution systems.

Server for the mcollective Application Server


%package common
Summary:        Application server for hosting Ruby code on any capable middleware
Group:          System Environment/Base
BuildRequires:	ruby2 >= 2.2.2
Requires:	ruby2 >= 2.2.2


%package client
Summary:        Client tools for the mcollective Application Server
Group:          Applications/System
BuildRequires:	ruby2 >= 2.2.2
Requires:	ruby2 >= 2.2.2, mcollective-common


%description common
The Marionette Collective AKA mcollective is a framework to build server orchestration or
parallel job execution systems.

Server for the mcollective Application Server


%description client
Client tools for the mcollective Application Server


%prep
%setup -q


%build
ls


%install
#rm -rf %{buildroot}
/opt/puppet/bin/ruby install.rb --destdir=%{buildroot}
sed -i 's#mcollectived="/usr/sbin/mcollectived"#mcollectived="/opt/puppet/sbin/mcollectived"#g' $RPM_BUILD_DIR/%{name}-%{version}/mcollective.init
mkdir $RPM_BUILD_ROOT/etc/init.d
cp $RPM_BUILD_DIR/%{name}-%{version}/mcollective.init $RPM_BUILD_ROOT/etc/init.d/mcollective
mkdir -p $RPM_BUILD_ROOT/etc/mcollective/ssl/clients
mkdir -p $RPM_BUILD_ROOT/etc/mcollective/plugin.d


%clean
#rm -rf %{buildroot}


%files 
%defattr(-,root,root,-)
%doc
/etc/init.d/mcollective
%config /etc/mcollective/facts.yaml
/etc/mcollective/plugin.d
%config /etc/mcollective/server.cfg
%config /etc/mcollective/client.cfg
/etc/mcollective/ssl
/opt/puppet/sbin/mcollectived


%files common
%defattr(-,root,root,-)
%doc
/etc/mcollective/data-help.erb
/etc/mcollective/discovery-help.erb
/etc/mcollective/metadata-help.erb
/etc/mcollective/rpc-help.erb
/opt/puppet/lib/ruby/site_ruby/2.2.0/mcollective
/opt/puppet/lib/ruby/site_ruby/2.2.0/mcollective.rb
/usr/libexec/mcollective


%files client
%defattr(-,root,root,-)
/opt/puppet/bin/mco


%changelog

