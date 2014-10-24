#ifndef __server_socket
#define __server_socket

#include "socket.h"

class ServerSocket : public Socket {
	public:
		ServerSocket(unsigned short port);
		~ServerSocket();

	private:
		inline void bind();

	public:
		/* public member functions */
		Socket *listen();
};

#endif
