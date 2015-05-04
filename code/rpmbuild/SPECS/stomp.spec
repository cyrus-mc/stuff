Name:           mcollective
Version:	2.8.1
Release:        1%{?dist}
Summary:	Application server for hosting Ruby code on any capable middleware

License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Source0:	http://downloads.puppetlabs.com/mcollective/mcollective-2.8.1.tar.gz
Group:		System Environment/Base

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	pe-ruby
AutoReqProv:	no


%description
The Marionette Collective AKA mcollective is a framework to build server orchestration or
parallel job execution systems.

Server for the mcollective Application Server

%prep
%setup -q


%build
ls


%install
/opt/puppet/bin/ruby install.rb --destdir=$RPM_BUILD_ROOT
# install the SysV init script
# need to replace location of mcollective binary
sed -i 's#mcollectived="/usr/sbin/mcollectived"#mcollectived="/opt/puppet/sbin/mcollectived"#g' $RPM_BUILD_DIR/%{name}-%{version}/mcollective.init
mkdir $RPM_BUILD_ROOT/etc/init.d
cp $RPM_BUILD_DIR/%{name}-%{version}/mcollective.init $RPM_BUILD_ROOT/etc/init.d/mcollective

%clean
# remove the build
#rm -rf $RPM_BUILD_ROOT
# remove the extracted source
#rm -rf $RPM_BUILD_DIR/%{name}-%{version}


%files
%defattr(-,root,root)
/opt/puppet
/etc/mcollective/data-help.erb
/etc/mcollective/discovery-help.erb
/etc/mcollective/metadata-help.erb
/etc/mcollective/rpc-help.erb
%config /etc/mcollective/client.cfg
%config /etc/mcollective/facts.yaml
%config /etc/mcollective/server.cfg
/etc/init.d/mcollective
/usr/libexec/mcollective


%post
# add mcollective init script to system
chkconfig --add mcollective

%changelog

