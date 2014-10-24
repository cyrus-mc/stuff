#ifndef __client_socket
#define __client_socket

#include "socket.h"

class ClientSocket : public Socket {
	public:
		ClientSocket(char *host, unsigned short port);
		~ClientSocket();

	private:
		/* public member functions */
		inline void connect();
};

#endif
