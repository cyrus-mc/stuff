Name:		ruby2
Version:	2.2.2	
Release:	1%{?dist}
Summary:	An interpreter of object-oriented scripting language

Group:		Development/Languages	
License:	Ruby or GPLv2	
URL:		http://www.ruby-lang.org/	
Source0:	ruby-2.2.2.tar.gz	
BuildRoot:	%(mktemp -ud %{_tmppath}/%{name}-%{version}-%{release}-XXXXXX)

#BuildRequires:	
#Requires:	
AutoReqProv:	no

%description
Ruby is the interpreted scripting language for quick and easy
object-oriented programming.  It has many features to process text
files and to do system management tasks (as in Perl).  It is simple,
straight-forward, and extensible.


%prep
%setup -n ruby-%{version}


%build
./configure --prefix=/opt/puppet
make %{?_smp_mflags}


%install
rm -rf %{buildroot}
make install DESTDIR=%{buildroot}

# build openssl ext
RUBYLIB=%{buildroot}/opt/puppet/lib/ruby/2.2.0:%{buildroot}/opt/puppet/lib/ruby/gems:%{buildroot}/opt/puppet/lib/ruby/site_ruby:%{buildroot}/opt/puppet/lib/ruby/vendor_ruby:%{buildroot}/opt/puppet/lib/ruby/2.2.0/x86_64-linux
export RUBYLIB
cd ext/openssl
#../../ruby extconf.rb
%{buildroot}/opt/puppet/bin/ruby extconf.rb
make top_srcdir="../../"
make install top_srcdir="../../"


%clean
rm -rf %{buildroot}


%files
%defattr(-,root,root,-)
%doc
/opt/puppet


%changelog

