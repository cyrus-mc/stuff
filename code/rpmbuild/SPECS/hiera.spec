Name:           hiera
Version:	1.3.4
Release:        1%{?dist}
Summary:	A simple pluggable Hierarchical Database.

License:	ASL 2.0
URL:		http://projects.puppetlabs.com/projects/hiera/
Source0:	https://downloads.puppetlabs.com/hiera/hiera-1.3.4.tar.gz
Group:		System Environment/Base

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	pe-ruby
AutoReqProv:	no


%description
A simple pluggable Hierarchical Database.

%prep
%setup -q


%build
ls


%install
/opt/puppet/bin/ruby install.rb --destdir=$RPM_BUILD_ROOT


%clean
# remove the build
rm -rf $RPM_BUILD_ROOT
# remove the extracted source
rm -rf $RPM_BUILD_DIR/%{name}-%{version}


%files
%defattr(-,root,root)
/opt/puppet
%config /etc/hiera.yaml


%changelog

