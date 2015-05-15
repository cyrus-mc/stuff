Name:           mcollective-service-agent
Version: 	3.1.3	
Release:        1%{?dist}
Summary:	Start and stop system services

License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Group:		System Tools
Source0:	http://puppetlabs.com/mcollective/mcollective-service-agent-3.1.3.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	mcollective-service-common
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
/usr/libexec/mcollective/mcollective/agent/service.rb


%changelog

