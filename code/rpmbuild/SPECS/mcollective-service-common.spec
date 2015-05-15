Name:           mcollective-service-common
Version:	3.1.3
Release:        1%{?dist}
Summary:	Start and stop system services

License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Group:		System Tools
Source0:	http://puppetlabs.com/mcollective/mcollective-service-common-3.1.3.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	mcollective
AutoReqProv:	no


%description
Start and stop system services


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
/usr/libexec/mcollective/mcollective/agent/service.ddl
/usr/libexec/mcollective/mcollective/data/service_data.ddl
/usr/libexec/mcollective/mcollective/data/service_data.rb
/usr/libexec/mcollective/mcollective/util/service
/usr/libexec/mcollective/mcollective/util/service/base.rb
/usr/libexec/mcollective/mcollective/util/service/puppetservice.rb
/usr/libexec/mcollective/mcollective/validator/service_name.ddl
/usr/libexec/mcollective/mcollective/validator/service_name.rb

%changelog

