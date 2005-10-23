#include <iostream.h>
#include <arpa/inet.h>
#include <sys/socket.h>
#include <unistd.h>
#include <stdlib.h>

#include "server_socket.h"
#include "socket_exception.h"

ServerSocket::ServerSocket(unsigned short port) {

#ifdef __DEBUG
	cout << "ServerSocket::ServerSocket(unsigned short)" << endl;
#endif

	m_addr.sin_addr.s_addr = htonl(INADDR_ANY);	/* specify interface */
	m_addr.sin_port = htons(port);

	bind();

}

ServerSocket::~ServerSocket() {

#ifdef __DEBUG
	cout << "ServerSocket::~ServerSocket()" << endl;
#endif
}

inline void ServerSocket::bind() {

#ifdef __DEBUG
	cout << "ServerSocket::bind()" << endl;
#endif

	if (::bind(m_sock, (struct sockaddr *) &m_addr, sizeof(m_addr)) < 0)
		throw SocketException("Couldn't bind to specified port");
}

Socket *ServerSocket::listen() {

	int new_socket;
	socklen_t size;
	struct sockaddr_in clntAddr;
	
#ifdef __DEBUG
	cout << "ServerSocket::listen()" << endl;
#endif

	if (::listen(m_sock, 5) < 0)
		throw SocketException("Couldn't bind to specified port");

	size = sizeof(clntAddr);
	if ((new_socket = ::accept(m_sock, (struct sockaddr *) &clntAddr, &size)) < 0)
		throw SocketException("Failed to accept new connection");

	cout << "New client accepted" << endl;
	Socket *socket = new Socket(new_socket);
	return socket;
}
