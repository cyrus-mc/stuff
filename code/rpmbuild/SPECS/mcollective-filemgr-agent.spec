Name:           mcollective-filemgr-agent
Version:	1.1.0
Release:        1%{?dist}
Summary:	File Manager

License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Group:		System Tools
Source0:	http://puppetlabs.com/mcollective/mcollective-filemgr-agent-1.1.0.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	mcollective-filemgr-common
AutoReqProv:	no


%description
File Manager


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
/usr/libexec/mcollective/mcollective/agent/filemgr.rb


%changelog

