Name:           mcollective-puppet-common
Version:	1.10.0
Release:        1%{?dist}
Summary:	Manage the puppet agent with MCollective

License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Group:		System Tools
Source0:	http://puppetlabs.com/mcollective/mcollective-puppet-common-1.10.0.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	mcollective
AutoReqProv:	no


%description
Manage the puppet agent with MCollective

%prep
%setup -q


%build
ls


%install
cp -rp $RPM_BUILD_DIR/%{name}-%{version} $RPM_BUILD_ROOT

%clean
# remove the build
rm -rf $RPM_BUILD_ROOT
# remove the extracted source
rm -rf $RPM_BUILD_DIR/%{name}-%{version}


%files
%defattr(-,root,root)
/usr/libexec/mcollective/mcollective/agent/puppet.ddl
/usr/libexec/mcollective/mcollective/data/puppet_data.ddl
/usr/libexec/mcollective/mcollective/data/puppet_data.rb
/usr/libexec/mcollective/mcollective/data/resource_data.ddl
/usr/libexec/mcollective/mcollective/data/resource_data.rb
/usr/libexec/mcollective/mcollective/util/puppet_agent_mgr
/usr/libexec/mcollective/mcollective/util/puppet_agent_mgr.rb
/usr/libexec/mcollective/mcollective/util/puppet_agent_mgr/mgr_v2.rb
/usr/libexec/mcollective/mcollective/util/puppet_agent_mgr/mgr_v3.rb
/usr/libexec/mcollective/mcollective/util/puppet_agent_mgr/mgr_windows.rb
/usr/libexec/mcollective/mcollective/util/puppet_server_address_validation.rb
/usr/libexec/mcollective/mcollective/util/puppetrunner.rb
/usr/libexec/mcollective/mcollective/validator/puppet_resource_validator.ddl
/usr/libexec/mcollective/mcollective/validator/puppet_resource_validator.rb
/usr/libexec/mcollective/mcollective/validator/puppet_server_address_validator.ddl
/usr/libexec/mcollective/mcollective/validator/puppet_server_address_validator.rb
/usr/libexec/mcollective/mcollective/validator/puppet_tags_validator.ddl
/usr/libexec/mcollective/mcollective/validator/puppet_tags_validator.rb
/usr/libexec/mcollective/mcollective/validator/puppet_variable_validator.ddl
/usr/libexec/mcollective/mcollective/validator/puppet_variable_validator.rb


%changelog

