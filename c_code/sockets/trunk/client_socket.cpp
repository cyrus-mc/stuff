#include <iostream.h>
#include <arpa/inet.h>
#include <sys/socket.h>
#include <netdb.h>
#include <stdlib.h>

#include "client_socket.h"
#include "socket_exception.h"

ClientSocket::ClientSocket(char *host, unsigned short port) {

	struct hostent *host_info;

#ifdef __DEBUG
	cout << "ClientSocket::ClientSocket(char *, unsigned short)" << endl;
#endif

	if ( (host_info = gethostbyname(host)) == NULL )
		throw SocketException("Could not resolve host server");

	m_addr.sin_addr.s_addr = *((unsigned long *)host_info->h_addr_list[0]); /* Server IP */
	m_addr.sin_port = htons(port);

	connect(); 
	// for some reason the bottom results in a seg fault. Reason might be
	// that gethostbyname has a static memory section for the structure it
	// returns and it can't be freed
//	free(host_info); /* free memory */
}

ClientSocket::~ClientSocket() {

#ifdef __DEBUG
	cout << "ClientSocket::~ClientSocket()" << endl;
#endif

}

inline void ClientSocket::connect() {

#ifdef __DEBUG
	cout << "ClientSocket::connect()" << endl;
#endif

	if (::connect(m_sock, (struct sockaddr *) &m_addr, sizeof(m_addr)) < 0)
		throw SocketException("Couldn't connect to host");

}
